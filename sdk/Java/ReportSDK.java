package com.guosen.kuanggong.apm;

import java.io.IOException;
import java.lang.management.ManagementFactory;
import java.net.InetAddress;
import java.net.UnknownHostException;
import java.util.Date;
import java.util.HashMap;
import java.util.Random;

/**
 * APM ReportSDK
 * 
 * @desc report program run times, Error Info and block run time
 * @author Intril <jj.comeback@gmail.com>
 */
public class ReportSDK {

	private static final String COMMA = ",";
	private int ratio;
	private boolean isNeedSend;
	private long apiInitTime;
	private int projectId;
	private int maxMsgLen;
	private long lastPointTime;
	private String hostname;
	private int port;
	
	// log point data
	private HashMap<String, Integer> execLogPoint = new HashMap<String, Integer>();
	private Random random = new Random();

	/**
	 * @fixme Deal with ALL exceptions!!!
	 * @param hostname
	 * @param port
	 * @param isNeedSend
	 * @param ratio
	 * @param apiInitTime
	 * @param projectId
	 * @param maxMsgLen
	 * @throws IOException
	 */
	public ReportSDK(String hostname, int port, boolean isNeedSend, int ratio, Long apiInitTime, int projectId,
			int maxMsgLen) {
		this.ratio = ratio;
		this.isNeedSend = isNeedSend;
		this.apiInitTime = apiInitTime;
		this.projectId = projectId;
		this.maxMsgLen = maxMsgLen;
		this.hostname = hostname;
		this.port = port;
		this.lastPointTime = apiInitTime;
	}

	/**
	 * to execute the code fragment
	 * 
	 * @param string
	 */
	public boolean setExeLogPoint(String point) {
		if (this.isNeedSend == false) {
			return false;
		}

		long endTime = System.currentTimeMillis();
		int execTime = (int) (endTime - this.lastPointTime);
		this.lastPointTime = endTime;
		
		/**
		 * @fixme use Integer.valueOf(i)
		 */
		this.execLogPoint.put(point, Integer.valueOf(execTime)); 
		return true;
	}
	
	/**
	 * send access log 
	 * @param module
	 * @param playerId
	 * @param cmd
	 * @return
	 */
	public boolean sendAccessLog(String module, String playerId, String cmd) {
		
		int ratio = random.nextInt(100);
		if (this.isNeedSend == false || ratio > this.ratio || module == null || cmd == null) {
			return false;
		}
		// string buffer
		StringBuffer logBuffer = new StringBuffer();
		logBuffer.append("Access,");
		// get loacalhost ip
		try {
			/**
			 * define constance for ","
			 */
			logBuffer.append(InetAddress.getLocalHost().toString() + COMMA);
		} catch (UnknownHostException e) {
		}
		logBuffer.append(playerId+COMMA).append(module+COMMA).append(cmd+COMMA).append("0,").append("200,").append(this.projectId+COMMA);
		// trace
		StackTraceElement trace = new Throwable().getStackTrace()[0];
		logBuffer.append(trace.getFileName()+COMMA).append(trace.getLineNumber()+COMMA).append(trace.getMethodName()+COMMA);
		// get pid
		String name = ManagementFactory.getRuntimeMXBean().getName();
		logBuffer.append(name.split("@")[0]+COMMA).append("0,");
		// msg
		String msg = this.execLogPoint.toString();
System.out.println("msg="+msg);
		if (msg.length() > this.maxMsgLen) {
			msg = msg.substring(0, this.maxMsgLen);
		}
		logBuffer.append(msg.replaceAll(COMMA, "&") + COMMA);
		// exectime
		long start_time = this.apiInitTime;
		long exectime = System.currentTimeMillis() - start_time;
		long request_time = System.currentTimeMillis() / 1000;
		logBuffer.append(exectime+COMMA).append("User-Agent,").append(request_time);
		
System.out.println("output="+logBuffer);
		
		this.sendSocket(logBuffer.toString());
		return true;
	}
	
	public boolean sendErrorLog(String module, String playerId, String cmd, int level, int errCode, String msg,
			String fileName, int fileLine, String funcName) throws IOException {

		if (this.isNeedSend == false || module == null || cmd == null) {
			return false;
		}
		StringBuffer logBuffer = new StringBuffer();
		logBuffer.append("Error,");
		
		// get loacalhost ip
		try {
			logBuffer.append(InetAddress.getLocalHost().toString() + COMMA);
		} catch (UnknownHostException e) {
		}
		logBuffer.append(playerId+COMMA).append(module+COMMA).append(cmd+COMMA).append(errCode+COMMA).append("0,").append(this.projectId+COMMA);
		// trace
		logBuffer.append(fileName+COMMA).append(fileLine+COMMA).append(funcName+COMMA);
		// get pid
		String name = ManagementFactory.getRuntimeMXBean().getName();
		logBuffer.append(name.split("@")[0]+COMMA).append(level+COMMA);
		// msg
		if (msg.length() > this.maxMsgLen) {
			msg = msg.substring(0, this.maxMsgLen);
		}
		logBuffer.append(msg.replaceAll(COMMA, "&") + COMMA);
		// exectime
		
		long request_time = System.currentTimeMillis() / 1000;
		logBuffer.append("0,").append("User-Agent,").append(request_time);
		this.sendSocket(logBuffer.toString());
		return true;
	}
	
	/**
	 * send socket data
	 * @param logData
	 * @throws IOException
	 */
	private void sendSocket(String logData) {
		/**
		 * @fixme Catch throwable in case of for fear of ALL errors!
		 */
		try {
			NioTcpClient client = new NioTcpClient(this.hostname, this.port);
			client.sendMsg(logData);
			client.close();
		} catch (Throwable e) {
			e.printStackTrace();
		}
	}
}
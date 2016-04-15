package com.guosen.kuanggong.apm;

import java.awt.List;
import java.io.BufferedReader;
import java.io.DataOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.PrintWriter;
import java.lang.management.ManagementFactory;
import java.lang.reflect.Array;
import java.net.InetAddress;
import java.net.Socket;
import java.net.UnknownHostException;
import java.nio.channels.SelectionKey;
import java.nio.channels.Selector;
import java.nio.channels.SocketChannel;
import java.util.ArrayList;
import java.util.Collection;
import java.util.Date;
import java.util.HashMap;
import java.util.Random;

import javax.swing.text.html.CSS;

import org.omg.CORBA.Request;

/**
 * APM ReportSDK
 * 
 * @desc report program run times, Error Info and block run time
 * @author Intril <jj.comeback@gmail.com>
 */
public class ReportSDK {

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

	public ReportSDK(String hostname, int port, boolean isNeedSend, int ratio, Long apiInitTime, int projectId,
			int maxMsgLen) throws IOException {
		this.ratio = ratio;
		this.isNeedSend = isNeedSend;
		this.apiInitTime = apiInitTime;
		this.projectId = projectId;
		this.maxMsgLen = maxMsgLen;
		this.hostname = hostname;
		this.port = port;
		// TODO Auto-generated constructor stub
	}

	/**
	 * to execute the code fragment
	 * 
	 * @param string
	 */
	public boolean setExeLogPoint(String point) {
		// TODO Auto-generated method stub
		if (this.isNeedSend == false) {
			return false;
		}
		long endTime = new Date().getTime();
		int execTime = (int) (endTime - this.lastPointTime);
		this.lastPointTime = endTime;
		this.execLogPoint.put(point, execTime);
		return true;
	}
	
	/**
	 * send access log 
	 * @param module
	 * @param playerId
	 * @param cmd
	 * @return
	 * @throws IOException
	 */
	public boolean sendAccessLog(String module, int playerId, String cmd) throws IOException {
		// TODO Auto-generated method stub
		Random random = new Random();
		int ratio = random.nextInt(100);
		if (this.isNeedSend == false || ratio > this.ratio || module == null || cmd == null) {
			return false;
		}
		// string buffer
		StringBuffer logBuffer = new StringBuffer();
		logBuffer.append("Access,");
		// get loacalhost ip
		try {
			logBuffer.append(InetAddress.getLocalHost().toString() + ",");
		} catch (UnknownHostException e) {
		}
		logBuffer.append(playerId+",").append(module+",").append(cmd+",").append("0,").append("200,").append(this.projectId+",");
		// trace
		StackTraceElement trace = new Throwable().getStackTrace()[0];
		logBuffer.append(trace.getFileName()+",").append(trace.getLineNumber()+",").append(trace.getMethodName()+",");
		// get pid
		String name = ManagementFactory.getRuntimeMXBean().getName();
		logBuffer.append(name.split("@")[0]+",").append("0,");
		// msg
		String msg = this.execLogPoint.toString();
		if (msg.length() > this.maxMsgLen) {
			msg = msg.substring(0, this.maxMsgLen);
		}
		logBuffer.append(msg.replace("&", " ") + ",");
		// exectime
		long start_time = this.apiInitTime;
		long exectime = new Date().getTime() - start_time;
		long request_time = new Date().getTime() / 1000;
		logBuffer.append(exectime+",").append("User-Agent,").append(request_time);
		this.sendSocket(logBuffer.toString());
		return true;
	}
	
	public boolean sendErrorLog(String module, int playerId, String cmd, int level, int errCode, String msg,
			String fileName, int fileLine, String funcName) throws IOException {
		// TODO Auto-generated method stub
		if (this.isNeedSend == false || module == null || cmd == null) {
			return false;
		}
		StringBuffer logBuffer = new StringBuffer();
		logBuffer.append("Error,");
		
		// get loacalhost ip
		try {
			logBuffer.append(InetAddress.getLocalHost().toString() + ",");
		} catch (UnknownHostException e) {
		}
		logBuffer.append(playerId+",").append(module+",").append(cmd+",").append(errCode+",").append("0,").append(this.projectId+",");
		// trace
		logBuffer.append(fileName+",").append(fileLine+",").append(funcName+",");
		// get pid
		String name = ManagementFactory.getRuntimeMXBean().getName();
		logBuffer.append(name.split("@")[0]+",").append(level+",");
		// msg
		if (msg.length() > this.maxMsgLen) {
			msg = msg.substring(0, this.maxMsgLen);
		}
		logBuffer.append(msg.replace("&", " ") + ",");
		// exectime
		
		long request_time = new Date().getTime() / 1000;
		logBuffer.append("0,").append("User-Agent,").append(request_time);
		this.sendSocket(logBuffer.toString());
		return true;
	}
	
	/**
	 * send socket data
	 * @param logData
	 * @throws IOException
	 */
	private void sendSocket(String logData) throws IOException{
		try {
			NioTcpClient client = new NioTcpClient(this.hostname, this.port);
			client.sendMsg(logData);
			client.close();
		} catch (IOException e) {
			
		}
	}
}
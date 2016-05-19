import java.io.IOException;

public class example {

	public static void main(String[] args) throws IOException {
		// Connect Socket Config
		String host = "172.24.180.96";
		int port = 6969;
		int ratio = 100;
		boolean isNeedSend = true;
		long apiInitTime = System.currentTimeMillis();
		int projectId = 1;
		int maxMsgLen = 2000;
		// ReportSDK
		ReportSDK ReportSDK = new ReportSDK(host, port, isNeedSend, ratio, apiInitTime, projectId, maxMsgLen);
		// start report
		ReportSDK.setExeLogPoint("start_sys");
		// first function
		testFunc();
		ReportSDK.setExeLogPoint("run Func-testFunc");
		String playerId = '13800000000';
		String module = "test_module";
		String cmd = "/example.java";
		try {
			// second function
			testFunc1();
		} catch (Exception e) {
			// TODO: handle exception
			ReportSDK.sendErrorLog(module, playerId, cmd, 1, 404, e.getMessage(), "example.java", 35, "test" );
		}

		ReportSDK.setExeLogPoint("run Func-testFunc1");
		// Report Module Config
		ReportSDK.sendAccessLog(module, playerId, cmd);
	}

	private static void testFunc() {
		// TODO Auto-generated method stub
		try {
			Thread.sleep(1000);
			System.out.println("Test testFunc... sleep 1000 ms");
		} catch (InterruptedException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}

	private static void testFunc1() {
		// TODO Auto-generated method stub
		try {
			Thread.sleep(1000);
			System.out.printf("Test testFunc1... sleep 1000 ms");
		} catch (InterruptedException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}

}

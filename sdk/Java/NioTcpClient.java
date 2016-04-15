package com.guosen.kuanggong.apm;

import java.io.IOException;
import java.net.InetSocketAddress;
import java.nio.ByteBuffer;
import java.nio.channels.SelectionKey;
import java.nio.channels.Selector;
import java.nio.channels.SocketChannel;

public class NioTcpClient {

	private Selector selector;
	SocketChannel socketChannel;
	private InetSocketAddress inetSocketAddress;

	public NioTcpClient(String hostname, int port) throws IOException {
		inetSocketAddress = new InetSocketAddress(hostname, port);
		initialize();
	}

	private void initialize() throws IOException {
		socketChannel = SocketChannel.open(inetSocketAddress);
		socketChannel.configureBlocking(false);

		selector = Selector.open();
		socketChannel.register(selector, SelectionKey.OP_READ);
	}

	public void sendMsg(String message) throws IOException {
		ByteBuffer writeBuffer = ByteBuffer.wrap(message.getBytes("UTF-8"));
		socketChannel.write(writeBuffer);
	}

	public void close() throws IOException {
		if (socketChannel != null && socketChannel.isConnected()) {
			socketChannel.close();
		}
	}
}
package com.example.chris.fstest;

import android.annotation.SuppressLint;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.text.Html;
import android.widget.TextView;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;

public class MainActivity extends AppCompatActivity {
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        // Hide the action bar
        if (getSupportActionBar() != null) {
            getSupportActionBar().hide();
        }
        setContentView(R.layout.activity_main);
        TextView tv1 = (TextView) findViewById(R.id.tv1);
        try {
            tv1.setText(Html.fromHtml(readTextFileFromAssets("content.txt"), Html.FROM_HTML_MODE_COMPACT));
        } catch (IOException e) {
            e.printStackTrace();
        }
    }
    private String readTextFileFromAssets(String fileName) throws IOException {
        BufferedReader reader1 = new BufferedReader(new InputStreamReader(getAssets().open(fileName)));
        StringBuilder sbd = new StringBuilder();
        String l1;
        while ((l1 = reader1.readLine()) != null) {
            sbd.append(l1).append("\n");
        }
        reader1.close();
        return sbd.toString();
    }
}

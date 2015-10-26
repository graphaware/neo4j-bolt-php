#!/bin/bash

export JAVA_HOME=/usr/lib/jvm/java-8-oracle
export JRE_HOME=/usr/lib/jvm/java-8-oracle

wget http://alpha.neohq.net/dist/neo4j-enterprise-3.0.0-M01-NIGHTLY-unix.tar.gz > null
mkdir neo
tar xzf neo4j-enterprise-3.0.0-M01-NIGHTLY-unix.tar.gz -C neo --strip-components=1 > null
sed -i.bak 's/^\(dbms\.security\.auth_enabled=\).*/\1false/' ./neo/conf/neo4j-server.properties
neo/bin/neo4j start
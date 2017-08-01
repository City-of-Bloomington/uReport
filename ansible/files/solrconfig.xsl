<?xml version="1.0"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="xml" encoding="UTF-8" indent="yes" />

    <xsl:template match="@*|node()">
        <xsl:copy>
            <xsl:apply-templates select="@*|node()" />
        </xsl:copy>
    </xsl:template>


    <xsl:template match="schemaFactory[@class='ManagedIndexSchemaFactory']">
        <xsl:text disable-output-escaping="yes">&lt;!--</xsl:text>
            <schemaFactory class="ManagedIndexSchemaFactory">
                <xsl:apply-templates select="@*|node()" />
            </schemaFactory>
        <xsl:text disable-output-escaping="yes">--&gt;</xsl:text>
        <schemaFactory class="ClassicIndexSchemaFactory"/>
    </xsl:template>

    <xsl:template match="processor[@class='solr.AddSchemaFieldsUpdateProcessorFactory']">
        <xsl:text disable-output-escaping="yes">&lt;!--</xsl:text>
            <processor class="solr.AddSchemaFieldsUpdateProcessorFactory">
                <xsl:apply-templates select="@*|node()" />
            </processor>
        <xsl:text disable-output-escaping="yes">--&gt;</xsl:text>
    </xsl:template>
</xsl:stylesheet>

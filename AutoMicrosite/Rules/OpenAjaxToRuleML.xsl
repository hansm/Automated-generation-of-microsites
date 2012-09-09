<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns="http://ruleml.org/spec"
	xmlns:oa="http://openajax.org/metadata"
	xmlns:oax="http://automicrosite.maesalu.com/OpenAjaxMetadataExtension"
	version="1.0">

	<xsl:param name="widget" select="999" />
	
	<xsl:template match="/">
		<RuleML>
			<Assert>
				<xsl:for-each select="//oa:category">
					<Atom>
						<Rel iri="http://openajax.org/metadata#category" />
						<slot><Ind>widget</Ind><Ind><xsl:value-of select="$widget" /></Ind></slot>
						<slot><Ind>category</Ind><Ind iri="@iri" /></Ind></slot>
					</Atom>
				</xsl:for-each>

				<Atom>
					<Rel iri="http://openajax.org/metadata#id" />
					<slot><Ind>widget</Ind><Ind><xsl:value-of select="$widget" /></Ind></slot>
					<slot><Ind>id</Ind><Ind><xsl:value-of select="//@id" /></Ind></slot>
				</Atom>

				<Atom>
					<Rel iri="http://openajax.org/metadata#width" />
					<slot><Ind>widget</Ind><Ind><xsl:value-of select="$widget" /></Ind></slot>
					<slot><Ind>width</Ind><Ind type="Integer"><xsl:value-of select="//@width" /></Ind></slot>
				</Atom>

				<Atom>
					<Rel iri="http://openajax.org/metadata#height" />
					<slot><Ind>widget</Ind><Ind><xsl:value-of select="$widget" /></Ind></slot>
					<slot><Ind>height</Ind><Ind type="Integer"><xsl:value-of select="//@height" /></Ind></slot>
				</Atom>
			</Assert>
		</RuleML>

	</xsl:template>

</xsl:stylesheet>
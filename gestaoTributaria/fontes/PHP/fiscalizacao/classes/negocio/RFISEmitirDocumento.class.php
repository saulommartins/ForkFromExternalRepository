<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Classe de regra de negócio para emissão de documentos
    * Data de Criação: 28/08/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Fellipe Esteves dos Santos

    * @package URBEM
    * @subpackage Regra

    * Casos de uso:

    $Id:$
*/
include_once ( CAM_OOPARSER."tbs_class.php" );
include_once ( CAM_OOPARSER."tbsooo_class.php" );
include_once ( CLA_ARQUIVO );

class RFISEmitirDocumento
{
    public function emitir($arParametros)
    {
        require_once( CAM_GT_FIS_NEGOCIO. $arParametros['stPrograma'].'.class.php');

        $objeto  	= new $arParametros['stPrograma'];
        $metodo 	= $arParametros['stMetodo'];

        unset($arParametros['stPrograma']);
        unset($arParametros['stMetodo']);

        $documento = $objeto->$metodo( $arParametros );

        $stPath = realpath($documento["nome_arquivo"]);

        if ($documento["nome_label"]=='') {
            $stArquivoAnexo = $documento["nome_arquivo"];
        } else {
            $stArquivoAnexo = $documento["nome_label"];
        }

        $resposta = "<script>\n";
        $resposta.= "window.open('".CAM_GT_FIS_ANEXOS."download.php?arquivo=".$stPath."&stNomeArquivo=".$stArquivoAnexo."');\n";
        //$resposta.= "window.close();\n";
        $resposta.= "</script>\n";

        sistemaLegado::executaFrameOculto('window.close();');
        //sistemaLegado::executaiFrameOculto('window.close("#")');
        return $resposta;
    }

    public function abrir($arDocumento)
    {
        $stPath = realpath($arDocumento["nome_arquivo"]);

        if ($arDocumento["nome_label"]) {
            $stArquivoAnexo = $arDocumento["nome_label"];
        } else {
            $stArquivoAnexo = $arDocumento["nome_arquivo"];
        }

        $resposta = CAM_GT_FIS_ANEXOS."download.php?arquivo=".$stPath."&stNomeArquivo=".$stArquivoAnexo;

        sistemaLegado::mudaFrameOculto($resposta);

    }

    public function listar($arParametros)
    {
        require_once( CAM_GT_FIS_NEGOCIO. $arParametros['stPrograma'].'.class.php');

        $objeto  	= new $arParametros['stPrograma'];
        $metodo 	= $arParametros['stMetodo'];

        unset($arParametros['stPrograma']);
        unset($arParametros['stMetodo']);

        $documentos = $objeto->$metodo( $arParametros );

        $resposta = "<script>\n";
        $resposta.= "window.opener.frames['telaPrincipal'].document.location = '".CAM_GT_FIS_INSTANCIAS."/processoFiscal/LSManterProcesso.php';\n";
        $resposta.= "window.close();\n";
        $resposta.= "</script>\n";

        return $resposta;
    }

    public function construir($stType, $stTemplate, $arParametros)
    {
        switch ( strtolower( $stType ) ) {
            case 'odt'	:
            return $this->converterODT( $stTemplate, $arParametros );
            break;

            case 'pdf'	:
            return $this->converterPDF( $stTemplate, $arParametros );
            break;
        }
    }

    public function converterODT($stTemplate, $arParametros)
    {
        $OOParser = new clsTinyButStrongOOo;

        $OOParser->SetZipBinary('zip');
        $OOParser->SetUnzipBinary('unzip');
        $OOParser->SetProcessDir('/tmp');

        $stDocumento = '/tmp/';

        $OOParser->_process_path = $stDocumento;

        $OOParser->NewDocFromTpl( $stTemplate );
        $OOParser->LoadXmlFromDoc('content.xml');

        foreach (array_keys($arParametros) as $array) {
            if (is_array($arParametros[$array][0]))
                $OOParser->MergeBlock( $array,  $arParametros[$array] );
            else
                $OOParser->MergeBlock( $array, array( $arParametros[$array] ) );
        }

        $OOParser->SaveXmlToDoc();

        $OOParser->LoadXmlFromDoc('styles.xml');
        $OOParser->SaveXmlToDoc();

        $arDocumento["nome_arquivo"]	= $OOParser->GetPathnameDoc();
        $arDocumento["tipo_arquivo"] 	= $OOParser->GetMimetypeDoc();

        return $arDocumento;
    }
}
?>

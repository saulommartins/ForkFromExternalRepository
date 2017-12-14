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
* Popup para inclusão de arquivos anexos aos processo
* Data de Criação: 15/05/2015

* @author Analista: Luciana Dellay
* @author Desenvolvedor: Michel Teixeira

$Id: OCDocumentoProcesso.php 62506 2015-05-15 16:23:58Z michel $

Casos de uso: uc-01.06.98
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_GA_PROT_MAPEAMENTO."TPROCopiaDigital.class.php";

$stJs = "<script>var d = window.parent.document;";

function montaListaAnexos($rsListaAnexos)
{
    $rsListaAnexos->setPrimeiroElemento();
     if ( !$rsListaAnexos->eof() ) {
         $obLista = new Lista;
         $obLista->setMostraPaginacao( false );
         $obLista->setRecordSet( $rsListaAnexos );
         $obLista->setTitulo ("Lista de Documentos");
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("&nbsp;");
         $obLista->ultimoCabecalho->setWidth( 8 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Anexo" );
         $obLista->ultimoCabecalho->setWidth( 80 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("&nbsp;");
         $obLista->ultimoCabecalho->setWidth( 2 );
         $obLista->commitCabecalho();

         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "anexo" );
         $obLista->commitDado();

         $obLista->addAcao();
         $obLista->ultimaAcao->setAcao( "EXCLUIR" );
         $obLista->ultimaAcao->setFuncao( true );
         $obLista->ultimaAcao->addCampo( "1","cod_copia" );
         $obLista->ultimaAcao->addCampo( "2","cod_documento" );
         $obLista->ultimaAcao->addCampo( "3","cod_processo" );
         $obLista->ultimaAcao->addCampo( "4","exercicio" );
         $obLista->ultimaAcao->setLink( "javascript: excluirDado('excluirAnexo');" );
         $obLista->commitAcao();
         $obLista->montaHTML();
         $stHTML = $obLista->getHTML();
         $stHTML = str_replace("\n","",$stHTML);
         $stHTML = str_replace("  ","",$stHTML);
         $stHTML = str_replace("'","\\'",$stHTML);
     } else {
         $stHTML = "";
     }

     return $stHTML;
}

function excluirAnexo($codCopia='', $codDocumento='', $codProcesso='', $exercicio='')
{
    if($codCopia!=''&&$codDocumento!=''&&$codProcesso!=''&&$exercicio!=''){        
        $obTPROCopiaDigital = new TPROCopiaDigital();
        $obTPROCopiaDigital->setDado('cod_copia'     , $codCopia);    
        $obTPROCopiaDigital->setDado('cod_documento' , $codDocumento);
        $obTPROCopiaDigital->setDado('cod_processo'  , $codProcesso);
        $obTPROCopiaDigital->setDado('exercicio'     , $exercicio);
        $obTPROCopiaDigital->recuperaPorChave($rsDocumentos);

        if($rsDocumentos->getNumLinhas()==1){
            $stDiretorio = CAM_PROTOCOLO."tmp/";
            $stAnexo = CAM_PROTOCOLO."anexos/";
            $lista = opendir($stDiretorio);
            
            while ($file = readdir($lista)) {
                if ($file == $rsDocumentos->getCampo('anexo')) {
                    $stDiretorio = $stDiretorio."/".$file;
                    //Apaga o arquivo do diretório TMP
                    if (unlink($stDiretorio)) {
                        SistemaLegado::exibeAvisoTelaPrincipal("Arquivo excluído com sucesso!","","");
                    }                    
                }
            }
            
            $lista = opendir($stAnexo);
            
            while ($file = readdir($lista)) {
                if ($file == $rsDocumentos->getCampo('anexo')) {
                    $stAnexo = $stAnexo."/".$file;
                    //Apaga o arquivo do diretório Anexo
                    unlink($stAnexo);                 
                }
            }
            
            $obTPROCopiaDigital = new TPROCopiaDigital();
            $obTPROCopiaDigital->setDado('cod_copia'     , $codCopia);    
            $obTPROCopiaDigital->setDado('cod_documento' , $codDocumento);
            $obTPROCopiaDigital->setDado('cod_processo'  , $codProcesso);
            $obTPROCopiaDigital->setDado('exercicio'     , $exercicio);
            $obTPROCopiaDigital->exclusao($boTransacao);
        }
        
        $obTPROCopiaDigital = new TPROCopiaDigital();  
        $obTPROCopiaDigital->setDado('cod_documento' , $codDocumento);
        $obTPROCopiaDigital->setDado('cod_processo'  , $codProcesso);
        $obTPROCopiaDigital->setDado('exercicio'     , $exercicio);
        $obTPROCopiaDigital->setCampoCod('');
        $obTPROCopiaDigital->recuperaPorChave($rsDocumentos);
        
        $stHTML = montaListaAnexos($rsDocumentos);
        
        $stJs .= "d.getElementById('spnListaAnexos').innerHTML='".$stHTML."';";
    }

    return $stJs;
}

switch ($_REQUEST["stCtrl"]) {
    case "montaListaAnexos":
        $obTPROCopiaDigital = new TPROCopiaDigital();
        $obTPROCopiaDigital->setDado('cod_documento' , $_REQUEST["inCodDocumento"]);
        $obTPROCopiaDigital->setDado('cod_processo'  , $_REQUEST["inCodProcesso"]);
        $obTPROCopiaDigital->setDado('exercicio'     , $_REQUEST["stAnoProcesso"]);
        $obTPROCopiaDigital->setCampoCod('');
        $obTPROCopiaDigital->recuperaPorChave($rsDocumentos);
        
        $stHTML = montaListaAnexos($rsDocumentos);
        
        $stJs .= "d.getElementById('spnListaAnexos').innerHTML='".$stHTML."';";
    break;
    case "excluirAnexo":
        $stJs .= excluirAnexo($_REQUEST["codCopia"], $_REQUEST["codDocumento"], $_REQUEST["codProcesso"], $_REQUEST["exercicio"]);
    break;
}

$stJs .= '</script>';

echo ( $stJs );
?>

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
    * Página de processamento oculto para o cadastro de natureza de transferência
    * Data de Criação   : 22/11/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Vitor Davi Valentini

    * @ignore

    * $Id: OCManterNaturezaTransferencia.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.16
*/

/*
$Log$
Revision 1.5  2006/09/18 10:31:03  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_CIM_NEGOCIO . "RCIMNaturezaTransferencia.class.php"    );

//Define o nome dos arquivos PHP
$stPrograma = "ManterNaturezaTransferencia";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obRegra      = new RCIMNaturezaTransferencia;
$rsDocumentos = new RecordSet;

function listaDocumentos($rsRecordSet, $boExecuta=true)
{
    global $obRegra;

    if ( $rsRecordSet->getNumLinhas() > 0 ) {

        $obLista = new Lista;
        $obLista->setMostraPaginacao                   ( false                                       );
        $obLista->setTitulo                            ( "Lista de documentos"                       );
        $obLista->setRecordSet                         ( $rsRecordSet                                );

        $obLista->addCabecalho                         (                                             );
        $obLista->ultimoCabecalho->addConteudo         ("&nbsp;"                                     );
        $obLista->ultimoCabecalho->setWidth            ( 3                                           );
        $obLista->commitCabecalho                      (                                             );

        $obLista->addCabecalho                         (                                             );
        $obLista->ultimoCabecalho->addConteudo         ( "Documento"                                 );
        $obLista->ultimoCabecalho->setWidth            ( 82                                          );
        $obLista->ultimoCabecalho->setClass            ( 'labelleft'                                 );
        $obLista->commitCabecalho                      (                                             );

        $obLista->addCabecalho                         (                                             );
        $obLista->ultimoCabecalho->addConteudo         ( "Obrigatório"                               );
        $obLista->ultimoCabecalho->setWidth            ( 10                                          );
        $obLista->ultimoCabecalho->setClass            ( 'labelleft'                                 );
        $obLista->commitCabecalho                      (                                             );

        $obLista->addCabecalho                         (                                             );
        $obLista->ultimoCabecalho->addConteudo         ("&nbsp;"                                     );
        $obLista->ultimoCabecalho->setWidth            ( 5                                           );
        $obLista->commitCabecalho                      (                                             );

        $obLista->addDado                              (                                             );
        $obLista->ultimoDado->setCampo                 ( "nome"                                      );
        $obLista->commitDado                           (                                             );

        $obLista->addDado                              (                                             );
        $obLista->ultimoDado->setCampo                 ( "obrigatorio"                               );
        $obLista->commitDado                           (                                             );

        $obLista->addAcao                              (                                             );
        $obLista->ultimaAcao->setAcao                  ( "EXCLUIR"                                   );
        $obLista->ultimaAcao->setFuncao                ( true                                        );
        $obLista->ultimaAcao->setLink                  ( "javascript:excluiDado('excluiDocumento');" );
        $obLista->ultimaAcao->addCampo                 ( "1","inId"                                  );
        $obLista->commitAcao                           (                                             );

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }
    // preenche a lista com innerHTML
    $stJs .= "d.getElementById('spnDocumentosNatureza').innerHTML = '".$stHtml."';";
    $stJs .= "f.stDescricaoDocumento.value = '';";
    $stJs .= "f.boObrigatorioDocumento.value = '';";

    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

// Acoes por pagina
switch ($_REQUEST['stCtrl']) {

    case "MontaDocumento":
        $boExisteDocumento = 'f';
        $arDocumentosSessao = Sessao::read('Documentos');
        if ( count( Sessao::read('Documentos')) - 1 >= 0 ) {
            foreach ($arDocumentosSessao as $inChave => $arDocumentos) {
                if ($arDocumentos["nome"] == $_POST[ 'stDescricaoDocumento' ]) {
                    $boExisteDocumento = 't';
                    break;
                }
            }
        }
        if ($boExisteDocumento == 't') {
            $stJs .= 'f.stDescricaoDocumento.value = "";';
            $stJs .= 'f.stDescricaoDocumento.focus();';
            $stJs .= "alertaAviso('@Documento já informado. (".$_POST["stDescricaoDocumento"].")','form','erro','".Sessao::getId()."');";
        } else {
            $rsRecordSet = new Recordset;
            $rsRecordSet->preenche                  ( $arDocumentosSessao           );
            $rsRecordSet->setUltimoElemento         (                               );
            $inUltimoId    = $rsRecordSet->getCampo ( "inId"                        );

            ++$inUltimoId;

            $arElementos[ 'inId'        ] = $inUltimoId;
            $arElementos[ 'codigo'      ] = 0;
            $arElementos[ 'nome'        ] = $_POST['stDescricaoDocumento'  ];
            $arElementos[ 'obrigatorio' ] = $_POST['boObrigatorioDocumento'];

            $arDocumentosSessao[] = $arElementos;

        Sessao::write('Documentos', $arDocumentosSessao );
            $rsRecordSet->preenche( $arDocumentosSessao );

            listaDocumentos       ( $rsRecordSet                  );
//            listaDocumentos       ( $arDocumentosSessao                  );
        }
    break;
    case "ListaDocumento":
        $rsRecordSet = new Recordset;
        $arDocumentosSessao = Sessao::read('Documentos'       );
        $rsRecordSet->preenche( $arDocumentosSessao           );
        listaDocumentos       ( $rsRecordSet                  );
    break;
    case "excluiDocumento":
        $rsRecordSet = new Recordset;

        $id = $_GET['inId'];
        $arDocumentosSessao = Sessao::read('Documentos');
        reset($arDocumentosSessao);

        while ( list( $arId ) = each( $arDocumentosSessao ) ) {
            if ($arDocumentosSessao[$arId]["inId"] != $id) {
                $arElementos[ 'inId'        ] = $arDocumentosSessao[$arId][ 'inId'        ];
                $arElementos[ 'codigo'      ] = $arDocumentosSessao[$arId][ 'codigo'      ];
                $arElementos[ 'nome'        ] = $arDocumentosSessao[$arId][ 'nome'        ];
                $arElementos[ 'obrigatorio' ] = $arDocumentosSessao[$arId][ 'obrigatorio' ];
                $arTMP[] = $arElementos;
            }
        }
        $arDocumentosSessao = $arTMP;
        if ( count( $arDocumentosSessao ) - 1 >= 0 ) {
            $rsRecordSet->preenche( $arDocumentosSessao );
        }
        Sessao::write('Documentos', $arDocumentosSessao);
        listaDocumentos( $rsRecordSet );
    break;
    case "buscaDescricao":
        $rsRecordSet = new RecordSet;
//        $obRegra->setDescricaoNatureza( $_POST['stDescricaoNatureza'] );
        $obRegra->listarNaturezaTransferencia( $rsRecordSet );
        $stComparaDescricao = strtolower( $_REQUEST["stDescricaoNatureza"] );
        while ( !$rsRecordSet->eof() ) {
            $stDescricaoRecordSet = strtolower( $rsRecordSet->getCampo("descricao") );
            if ($stComparaDescricao == $stDescricaoRecordSet) {
                $stJs .= 'f.stDescricaoNatureza.value = "";';
                $stJs .= 'f.stDescricaoNatureza.focus();';
                $stJs .= "alertaAviso('@Natureza já cadastrada. (".$rsRecordSet->getCampo("descricao").")','form','erro','".Sessao::getId()."');";
                break;
            }
            $rsRecordSet->proximo();
        }
    break;
    case "setaFocus":
        $stJs .= "f.stDescricaoNatureza.focus();";
    break;
    case "limparFormulario":
        Sessao::write('Documentos', array());
        $stJs .= "d.frm.reset();";
        $stJs .= "d.getElementById('spnDocumentosNatureza').innerHTML = '';";
    break;
}
if($stJs)
    SistemaLegado::executaFrameOculto($stJs);
?>

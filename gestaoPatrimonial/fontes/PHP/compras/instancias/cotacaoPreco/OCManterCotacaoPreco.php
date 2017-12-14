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
    * Página Oculta de Manter Cotação de Preço
    * Data de Criação   : 18/09/2006

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Cleisson Barboza

    * @ignore

    Casos de uso: uc-03.04.04

    $Id: OCManterCotacaoPreco.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

function listaFornecedores($arRecordSet)
{
    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arRecordSet );
    $rsRecordSet->addFormatacao('stNomFornecedor', 'HTML');
    if ( $rsRecordSet->getNumLinhas() != 0 ) {

        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );

        $obLista->setRecordSet( $rsRecordSet );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Código" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Nome do Fornecedor" );
        $obLista->ultimoCabecalho->setWidth( 75 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Ação");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "inCodFornecedor" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "stNomFornecedor" );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "javascript:excluiFornecedor('excluiFornecedor');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->montaHTML();

        $html = $obLista->getHTML();

        $html = str_replace("\n","",$html);
        $html = str_replace("  ","",$html);
        $html = str_replace("'","\\'",$html);

    }

    $stJs .= "d.getElementById('spnFornecedor').innerHTML = '".$html."';";

    return $stJs;
}

function limparFornecedores()
{
    $stJs .= " d.getElementById('stNomFornecedor').innerHTML = '&nbsp;';\n ";
    $stJs .= " f.inCodFornecedor.value = '';\n ";

    return $stJs;
}

switch ($stCtrl) {
    case 'incluirmontaListaFornecedores':
        $boIncluir = true;
        $stMensagem = "";

        $fornecedores = Sessao::read('fornecedores');

        if (!$_REQUEST['inCodFornecedor']) {
            $stMensagem = "Fornecedor não pode ser nulo.";
            $boIncluir = false;
        } elseif ( count( $fornecedores ) > 0 ) {

            $stChave = $_REQUEST['inCodFornecedor'];

            foreach ($fornecedores as $key => $array) {

                $stChaveFornecedor = $array['inCodFornecedor'];

                if ($stChave == $stChaveFornecedor) {
                    $boIncluir = false;
                    $stMensagem = "Este registro já existe na lista.";
                    break;
                }
            }
        }

        if ($boIncluir) {
            $inId = count( $fornecedores ) + 1;

            $arItens['inId'            ] = $inId;
            $arItens['inCodFornecedor' ] = $_REQUEST['inCodFornecedor'];
            $arItens['stNomFornecedor' ] = $_REQUEST['stNomFornecedor'];

            $fornecedores[] = $arItens;
            $stJs .= listaFornecedores( $fornecedores );
            $stJs .= limparFornecedores();
        } else {
            // mudado para funcionar com Ajax
            $stJs = "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');\n";
        }
        Sessao::write('fornecedores' , $fornecedores);
    break;
    case 'limpaFornecedores':
        $fornecedores = Sessao::read('fornecedores');
        $stJs .= limparFornecedores();
        $fornecedores = array();
        $stJs .= "d.getElementById('spnFornecedor').innerHTML = ''";
        Sessao::write('fornecedores', $fornecedores);
    break;
    case 'excluiFornecedor':
        $arVariaveis = $arTMP = array();
        $id = $_REQUEST['inId'];
        $inCount = 0;
        $fornecedores = Sessao::read('fornecedores');
        foreach ($fornecedores as $campo => $valor) {
            if ($fornecedores[$campo]['inId'] != $id) {
                $arFornecedores['inId'            ] =  ++$inCount;
                $arFornecedores['inCodFornecedor' ] = $fornecedores[$campo]['inCodFornecedor'];
                $arFornecedores['stNomFornecedor' ] = $fornecedores[$campo]['stNomFornecedor'];
                $arTMP[] = $arFornecedores;
            }
        }
        $fornecedores = $arTMP;
        $stJs .= listaFornecedores( $arTMP );
        Sessao::write('fornecedores' , $fornecedores);
    break;
}

echo $stJs;

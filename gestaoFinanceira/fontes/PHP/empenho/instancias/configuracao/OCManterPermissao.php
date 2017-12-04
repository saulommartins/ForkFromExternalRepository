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
    * Página Oculta de Permissão Autorização
    * Data de Criação   : 04/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Gelson W. Gonçalves

    * @ignore

    $Revision: 30805 $
    $Name$
    $Author: vitor $
    $Date: 2007-04-05 15:11:46 -0300 (Qui, 05 Abr 2007) $

    * Casos de uso: uc-02.03.01
*/

/*
$Log$
Revision 1.10  2007/04/05 18:11:16  vitor
8264

Revision 1.9  2006/07/14 20:59:57  leandro.zis
Bug #6181#

Revision 1.8  2006/07/05 20:47:34  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_EMP_NEGOCIO . "REmpenhoPermissaoAutorizacao.class.php"    );

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obRegra = new REmpenhoPermissaoAutorizacao;

function montaListaPermissoes($arRecordSet , $boExecuta = true)
{
        $rsPermissoes = new RecordSet;
        $rsPermissoes->preenche( $arRecordSet );
        $obLista = new Lista;
        $obLista->setMostraSelecionaTodos( true );
        $obLista->setTitulo('Selecione os Órgãos/Unidades que o Usuário tem Permissão');
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsPermissoes );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Órgão ");
        $obLista->ultimoCabecalho->setWidth( 6 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Descrição Órgão ");
        $obLista->ultimoCabecalho->setWidth( 40 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Unidade ");
        $obLista->ultimoCabecalho->setWidth( 6 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Descrição Unidade ");
        $obLista->ultimoCabecalho->setWidth( 40 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "num_orgao" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "nom_orgao" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "num_unidade" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "nom_unidade" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();

        $obChkPermitido = new CheckBox;
        $obChkPermitido->setName( "perm" );
        $obChkPermitido->setValue ("[num_orgao]_[num_unidade]");

        $obLista->addDadoComponente( $obChkPermitido );
        $obLista->ultimoDado->setCampo ("permitido" );
        $obLista->ultimoDado->setAlinhamento ( 'CENTRO' );
        $obLista->commitDadoComponente();

        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );

        if ($boExecuta) {
            SistemaLegado::executaFrameOculto("d.getElementById('spnListaPermissoes').innerHTML = '".$stHTML."'; LiberaFrames(true,false);");

        } else {
            return $stHTML;
        }

}

switch ($stCtrl) {
    case 'buscaUsuario':
        if ($_POST["inNumCGM"] != "") {
            $obRegra->obRUsuario->obRCGM->setNumCGM( $_POST["inNumCGM"] );
            $obRegra->obRUsuario->consultarUsuario( $rsCGM );
            $stNomCGM = $rsCGM->getCampo( "nom_cgm" );
            if (!$stNomCGM) {
                $js .= 'f.inNumCGM.value = "";';
                $js .= 'f.inNumCGM.focus();';
                $js .= 'd.getElementById("stNomCGM").innerHTML = "&nbsp;";';
                $js .= "alertaAviso('@Valor inválido. (".$_POST["inNumCGM"].")','form','erro','".Sessao::getId()."');";
            } else {
                $js .= 'd.getElementById("stNomCGM").innerHTML = "'.$stNomCGM.'";';
            }
        } else $js .= 'd.getElementById("stNomCGM").innerHTML = "&nbsp;";';
        SistemaLegado::executaFrameOculto($js);
    break;

    case 'montaListaPermissoes':
            //
            // Monta lista das permissoes atuais (existentes no BD) para o usuário informado
            //
            $obRegra   = new REmpenhoPermissaoAutorizacao;
            $rsOrgao   = new RecordSet;
            $rsUnidade = new RecordSet;

            $obRegra->setExercicio      ( Sessao::getExercicio() );
            $obRegra->obRUsuario->obRCGM->setNumCGM( $_POST['inNumCGM'] );

            $obRegra->listar( $rsPermissoes ) ;
            $arPermissoesAtu = array();
            $inCount = 0 ;
            while (!$rsPermissoes->eof()) {
                //$obRegra->setExercicio ( Sessao::getExercicio() ) ;
                //$obRegra->obRUsuario->obRCGM->setNumCGM( $rsPermissoes->getCampo( 'numcgm' ));
                  $obRegra->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $rsPermissoes->getCampo('num_orgao') );
                  $obRegra->obROrcamentoUnidade->setNumeroUnidade( $rsPermissoes->getCampo('num_unidade') );

                //$obRegra->listarOrgaoDespesaEntidadeUsuario( $rsOrgao );
                //$obRegra->listarUnidadeDespesaEntidadeUsuario( $rsUnidade );
                  $obRegra->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->listar($rsOrgao);
                  $obRegra->obROrcamentoUnidade->listar( $rsUnidade);

                  $stNomOrgao   = $rsOrgao->getCampo('nom_orgao');
                  $stNomUnidade = $rsUnidade->getCampo('nom_unidade');

                  $arPermissoesAtu[$inCount]['id']          = $inCount+1;
                  $arPermissoesAtu[$inCount]['num_orgao']   = $rsPermissoes->getCampo('num_orgao');
                  $arPermissoesAtu[$inCount]['nom_orgao']   = $stNomOrgao;
                  $arPermissoesAtu[$inCount]['num_unidade'] = $rsPermissoes->getCampo('num_unidade');
                  $arPermissoesAtu[$inCount]['nom_unidade'] = $stNomUnidade;
                  $arPermissoesAtu[$inCount]['permitido'] = true;
                  $inCount++;
                  $rsPermissoes->proximo();
             }
            //
            // Monta lista GERAL de possíveis permissões
            //
            $obRegra   = new REmpenhoPermissaoAutorizacao;
            $rsOrgao   = new RecordSet;
            $rsUnidade = new RecordSet;

            $obRegra->setExercicio ( Sessao::getExercicio() ) ;
            $stOrder = " ORDER BY oo.num_orgao";
            $obRegra->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->listar($rsOrgao, $stOrder);
            $arPermissoes = array();
            $inCount = 0;

            while (!$rsOrgao->eof()) {
                $obRegra->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $rsOrgao->getCampo("num_orgao") );
                $obRegra->obROrcamentoUnidade->listar( $rsUnidade, "unidade.num_unidade" );
             // $obRegra->listarUnidadeDespesaEntidadeUsuario( $rsUnidade );

                while ( !$rsUnidade->eof() ) {
                    $inCodUnidade   = $rsUnidade->getCampo("num_unidade");
                    $stNomUnidade   = $rsUnidade->getCampo("nom_unidade");

                    $inCodOrgao     = $rsOrgao->getCampo("num_orgao");
                    $stNomOrgao     = $rsOrgao->getCampo("nom_orgao");

                    $arPermissoes[$inCount]['id']          = $inCount+1;
                    $arPermissoes[$inCount]['num_orgao']   = $inCodOrgao;
                    $arPermissoes[$inCount]['nom_orgao']   = $stNomOrgao;
                    $arPermissoes[$inCount]['num_unidade'] = $inCodUnidade;
                    $arPermissoes[$inCount]['nom_unidade'] = $stNomUnidade;
                    $arPermissoes[$inCount]['permitido']   = false;

                    $inCount++;
                    $rsUnidade->PROXImo();
                }
                $rsOrgao->proximo();
            }
            //
            // Compara os dois array's de permissões e marca com checked (['permitido'] = true)
            // as permissões atuais (vindas do BD) na lista geral de possíveis permissões (Geral)
            //

            $inTamAtual = count($arPermissoesAtu); // Permissoes atuais
            $inTamGeral = count($arPermissoes); // Permissoes gerais

            if (!$inTamAtual == 0 ){ // Caso não haja permissões no BD sai para levar a lista geral sem marcações (checked's)
                for ($i = 0; $i < $inTamGeral; $i++) {
                    for ($j = 0; $j < $inTamAtual; $j++) {
                        if ($arPermissoesAtu[$j]['num_orgao'] == $arPermissoes[$i]['num_orgao']) {
                            if ($arPermissoesAtu[$j]['num_unidade'] == $arPermissoes[$i]['num_unidade']) {
                                $arPermissoes[$i]['permitido'] = true;
                            }
                        }
                    }
                    reset($arPermissoesAtu); // Volta para o primeiro registro das permissoes atuais
                }
                reset($arPermissoes); // Deixa no primeiro registro das permissões definitivas
            }
            Sessao::write('arPermissoes', $arPermissoes);
            montaListaPermissoes( Sessao::read('arPermissoes') );
    break;
}
?>

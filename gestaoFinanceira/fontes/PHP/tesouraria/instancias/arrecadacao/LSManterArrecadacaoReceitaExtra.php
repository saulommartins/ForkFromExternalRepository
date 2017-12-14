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
    * Página de Formulário para Lista de Estorno de Arrecadação Extra Orçamentárias
    * Data de Criação   : 14/09/2006
    *
    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Anderson C. Konze

    * @ignore

    $Id: LSManterArrecadacaoReceitaExtra.php 59612 2014-09-02 12:00:51Z gelson $

    $Revision: 27052 $
    $Name$
    $Author: cako $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.04.26

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"    );

//Define o nome dos arquivos PHP
$stPrograma = "ManterArrecadacaoReceitaExtra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FMManterArrecadacaoReceitaExtraEstorno.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
if ( $request->get('stAcao') ) {
    $stAcao = "alterar";
}

$stFiltro = "";
$boTransacao = "";
$stOrder = "";
//MANTEM FILTRO E PAGINACAO
$stLink = "&stAcao=".$stAcao;

$arLink = Sessao::read('link');
if ( $request->get('pg') and  $request->get('pos') ) {
   $stLink .= "&pg=".$request->get('pg');
   $stLink .= "&pos=".$request->get('pos');
   $arLink["pg"]  = $request->get('pg');
   $arLink["pos"] = $request->get('pos');
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if (!is_null($arLink)) {
    $_REQUEST = $arLink;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $arLink[$key] = $valor;
    }
}
Sessao::write('link', $arLink);

include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaTransferencia.class.php" );
$obTTesourariaTransferencia = new TTesourariaTransferencia();

if (isset($_REQUEST['inCodRecibo']))       $obTTesourariaTransferencia->setDado('inCodRecibo',         $_REQUEST['inCodRecibo']      );
if (isset($_REQUEST['inCodEntidade']))     $obTTesourariaTransferencia->setDado('inCodEntidade',       $_REQUEST['inCodEntidade']    );
if (isset($_REQUEST['stDtBoletim']))       $obTTesourariaTransferencia->setDado('stDtBoletim',         $_REQUEST['stDtBoletim']      );
if (isset($_REQUEST['inCodBoletim']))      $obTTesourariaTransferencia->setDado('inCodBoletim',        $_REQUEST['inCodBoletim']     );
if (isset($_REQUEST['inCodCredor']))       $obTTesourariaTransferencia->setDado('inCodCredor',         $_REQUEST['inCodCredor']      );
if (isset($_REQUEST['inCodRecurso']))      $obTTesourariaTransferencia->setDado('inCodRecurso',        $_REQUEST['inCodRecurso']     );
if (isset($_REQUEST['inCodPlanoDebito']))  $obTTesourariaTransferencia->setDado('inCodPlanoDebito',    $_REQUEST['inCodPlanoDebito'] );
if (isset($_REQUEST['inCodPlanoCredito'])) $obTTesourariaTransferencia->setDado('inCodPlanoCredito',   $_REQUEST['inCodPlanoCredito']);
$obTTesourariaTransferencia->setDado('inCodTipo', 2 ); // tesouraria.tipo_transferencia -> Arrecadação Extra
                                    $obTTesourariaTransferencia->setDado('stExercicio', Sessao::getExercicio() );

if($request->get('inCodUso') && $request->get('inCodDestinacao') && $request->get('inCodEspecificacao'))
    $obTTesourariaTransferencia->setDado('stDestinacaoRecurso', $request->get('inCodUso').".".$request->get('inCodDestinacao').".".$request->get('inCodEspecificacao') );

    $obTTesourariaTransferencia->setDado('inCodDetalhamento', $request->get('inCodDetalhamento') );

$stFiltro .= " AND (coalesce(t.valor,0.00) - coalesce(te.valor,0.00)) > 0.00 ";
$stFiltro .= " AND NOT EXISTS ( SELECT topr.cod_lote
                                  FROM tesouraria.transferencia_ordem_pagamento_retencao as topr
                                 WHERE topr.cod_lote     = t.cod_lote
                                   AND topr.cod_entidade = t.cod_entidade
                                   AND topr.exercicio    = t.exercicio
                                   AND topr.tipo         = t.tipo
                              )   ";

$obTTesourariaTransferencia->recuperaRelacionamento( $rsLista, $stFiltro, $stOrder, $boTransacao );

$rsLista->addFormatacao( 'valor' , 'NUMERIC_BR' );
$rsLista->addFormatacao( 'valor_estornado', 'NUMERIC_BR');

$obLista = new Lista;

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Recibo");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Boletim");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Data");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Lote");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Conta Receita");
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Conta a Crédito");
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Recurso");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor Arrecadado");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor Estornado");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_recibo" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_boletim]/[exercicio]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "dt_boletim" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_lote]/[exercicio]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_plano_credito] - [nom_conta_credito]" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_plano_debito] - [nom_conta_debito]" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "recurso" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[valor]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[valor_estornado]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( 'Estornar' );

$obLista->ultimaAcao->addCampo( "&inCodRecibo"       , "cod_recibo" );
$obLista->ultimaAcao->addCampo( "&inCodEntidade"     , "cod_entidade"      );
$obLista->ultimaAcao->addCampo( "&stNomEntidade"     , "nom_entidade"      );
$obLista->ultimaAcao->addCampo( "&inCodLote"         , "cod_lote"     );
$obLista->ultimaAcao->addCampo( "&inCodPlanoDebito"  , "cod_plano_credito" ); // Inverte para estornar
$obLista->ultimaAcao->addCampo( "&stNomContaDebito"  , "nom_conta_credito" ); // "
$obLista->ultimaAcao->addCampo( "&inCodPlanoCredito" , "cod_plano_debito" );  // "
$obLista->ultimaAcao->addCampo( "&stNomContaCredito" , "nom_conta_debito" ); // "
$obLista->ultimaAcao->addCampo( "&inCodRecurso"      , "cod_recurso" );
$obLista->ultimaAcao->addCampo( "&stMascRecurso"     , "masc_recurso_red" );
$obLista->ultimaAcao->addCampo( "&stNomRecurso"      , "nom_recurso" );
$obLista->ultimaAcao->addCampo( "&nuValorArrecadado" , "valor" );
$obLista->ultimaAcao->addCampo( "&inCodHistorico"    , "cod_historico" );
$obLista->ultimaAcao->addCampo( "&inCodCredor"       , "cod_credor" );
$obLista->ultimaAcao->addCampo( "&stNomCredor"       , "nom_credor" );
$obLista->ultimaAcao->addCampo( "&dtBoletimArrecadacao","dt_boletim" );
$obLista->ultimaAcao->addCampo( "&stTipo"            , "tipo" );

$obLista->ultimaAcao->setLink ( $pgForm."?stAcao=".$stAcao."&".Sessao::getId() );

$obLista->commitAcao();
$obLista->show();
?>

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
    * Página de geração do recordSet para o Relatório Metas de Execução da Despesa
    * Data de Criação   : 04/12/2006

    * @author Analista: Gelson Wolowski Goncalves
    * @author Desenvolvedor: Alessandro La-Rocca Silveira

    * @ignore

    $Revision: 22928 $
    $Name$
    $Autor: $
    $Date: 2007-05-29 10:41:09 -0300 (Ter, 29 Mai 2007) $

    * Casos de uso: uc-03.03.11
*/
/*
    $log: $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_PDF."RRelatorio.class.php"                                                    );
include_once ( TALM."TAlmoxarifadoRequisicaoItens.class.php"                                        );

$obTAlmoxarifadoRequisicaoItens = new TAlmoxarifadoRequisicaoItens();

$inCount = 0;

$arrayValores = Sessao::read("Valores");

echo "teste";

##sessao->transf['Valores']

foreach ($arrayValores  as $valor => $chave) {
    $obTAlmoxarifadoRequisicaoItens->setDado( 'cod_almoxarifado' , $chave['inCodAlmoxarifado'] );
    $obTAlmoxarifadoRequisicaoItens->setDado( 'cod_requisicao'   , $chave['inCodRequisicao']   );
    $obTAlmoxarifadoRequisicaoItens->setDado( 'exercicio'        , $chave['stExercicio']       );
    $obTAlmoxarifadoRequisicaoItens->setDado( 'cod_item'         , $chave['cod_item']          );
    $obTAlmoxarifadoRequisicaoItens->setDado( 'cod_marca'        , $chave['cod_marca']         );
    $obTAlmoxarifadoRequisicaoItens->setDado( 'cod_centro'       , $chave['cod_centro']        );
    $obTAlmoxarifadoRequisicaoItens->recuperaSaldoAtendido( $rsRecorSet );

    Sessao::write("Valores[".$inCount."][saldo_atend]",number_format(abs($rsRecorSet->getCampo('saldo_atendido')), 4, ',', '.'));
    ##sessao->transf['Valores'][$inCount]['saldo_atend'] = number_format(abs($rsRecorSet->getCampo('saldo_atendido')), 4, ',', '.');
    $inCount++;
}

$arTemp  = array();
$inCount = 0;

$arrayValores = Sessao::read("Valores");

for ( $i=0; $i<= count($arrayValores); $i++ ) {
    if ($arrayValores[$i]['quantidade'] > 0) {
        $arTemp[$inCount] = $arrayValores[$i];
        $inCount;
    }
}

##sessao->transf['Valores'] = array();
##sessao->transf['Valores'] = $arTemp;

Sessao::write("Valores",$arTemp);

$obRelatorio = new RRelatorio;

$obRelatorio->executaFrameOculto( 'OCGeraRelatorioSaida.php' );

?>

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
    * Página de processamento de Contas para INcrição de RP
    * Data de Criação   : 02/10/2006

    * @author Analista: Cleisson Barbosa
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    * $Id: PRDefinirContasRP.php 63831 2015-10-22 12:51:00Z franver $

    * Casos de uso: uc-02.02.31
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadeTipoContaLancamentoRp.class.php';
include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadeContaLancamentoRp.class.php';

$stPrograma = "DefinirContasRP";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

Sessao::setTrataExcecao( true );

$obTContabilidadeContaLancamentoRp = new TContabilidadeContaLancamentoRp;
$obTContabilidadeContaLancamentoRp->excluiTudo( " WHERE exercicio = '".Sessao::getExercicio()."' ");

$arContasDebito = Sessao::read('arContasDebito');
$arContasCreditos = Sessao::read('arContasCredito');

if (is_array($arContasDebito)) {
    foreach ($arContasDebito as $arConta) {
        $obTContabilidadeContaLancamentoRp->setDado ( 'exercicio'      , Sessao::getExercicio() );
        $obTContabilidadeContaLancamentoRp->setDado ( 'cod_entidade'   , $arConta['cod_entidade'] );
        $obTContabilidadeContaLancamentoRp->setDado ( 'cod_tipo_conta' , $arConta['cod_tipo']     );
        $obTContabilidadeContaLancamentoRp->setDado ( 'cod_plano'      , $arConta['cod_conta']    );
        $obTContabilidadeContaLancamentoRp->inclusao();
    }
}

if (is_array($arContasCreditos)) {
    foreach ($arContasCreditos as $arConta) {
        $obTContabilidadeContaLancamentoRp->setDado ( 'exercicio'      , Sessao::getExercicio() );
        $obTContabilidadeContaLancamentoRp->setDado ( 'cod_entidade'   , $arConta['cod_entidade'] );
        $obTContabilidadeContaLancamentoRp->setDado ( 'cod_tipo_conta' , $arConta['cod_tipo']     );
        $obTContabilidadeContaLancamentoRp->setDado ( 'cod_plano'      , $arConta['cod_conta']    );
        $obTContabilidadeContaLancamentoRp->inclusao();
    }
}

Sessao::encerraExcecao();

sistemaLegado::alertaAviso($pgForm, 'Contas definidas.' ,"incluir","aviso", Sessao::getId(), "../");

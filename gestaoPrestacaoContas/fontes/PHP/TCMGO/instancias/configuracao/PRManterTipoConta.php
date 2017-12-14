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
    * Página de Processamento
    * Data de Criação   : 16/04/2007

    * @author Henrique Boaventura

    * @ignore

    $Id: PRManterTipoConta.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TTGO.'TTGOGrupoPlanoConta.class.php');

//Define o nome dos arquivos PHP
$stPrograma = "ManterTipoConta";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

Sessao::setTrataExcecao ( true );
$stAcao = $request->get('stAcao');
$arContas    = Sessao::read('arContas');

switch ($_REQUEST['stAcao']) {
    case 'incluir' :

        if ($arContas > 0) {
            $obTTGOGrupoPlanoConta = new TTGOGrupoPlanoConta();

            $obTTGOGrupoPlanoConta->setDado('exercicio',Sessao::getExercicio());
            $obTTGOGrupoPlanoConta->setDado('cod_tipo_lancamento',$_REQUEST['inTipoLancamento']);
            $obTTGOGrupoPlanoConta->setDado('cod_tipo',$_REQUEST['inTipoConta']);
            $obTTGOGrupoPlanoConta->recuperaGrupoPlanoConta( $rsContas );
            while ( !$rsContas->eof() ) {
                $obTTGOGrupoPlanoConta->setDado('cod_conta',$rsContas->getCampo('cod_conta'));
                $obTTGOGrupoPlanoConta->exclusao();
                $rsContas->proximo();
            }
            foreach ($arContas as $arAux) {
                $obTTGOGrupoPlanoConta->setDado('cod_conta',$arAux['cod_conta']);
                $obTTGOGrupoPlanoConta->inclusao();
            }
            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode('É necessário cadastrar pelo uma conta!'),"n_incluir","erro");
        }
}

Sessao::encerraExcecao();

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
    * Data de Criação   : 10/05/2007

    * @author Henrique Boaventura

    * @ignore

    * Casos de uso : uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( TTGO.'TTGOBalanceteExtmmaa.class.php' );

//Define o nome dos arquivos PHP
$stPrograma = "ManterExtmmaa";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

Sessao::setTrataExcecao ( true );
$stAcao = $request->get('stAcao');
$arContas    = Sessao::read('arContas');
$arExcluidas = Sessao::read('arExcluidas');

switch ($_REQUEST['stAcao']) {
    case 'configurar' :

        $obTTGOBalanceteExtmmaa = new TTGOBalanceteExtmmaa();
        $obTTGOBalanceteExtmmaa->setDado( 'exercicio', Sessao::getExercicio() );
        if ( count( $arExcluidas ) > 0 ) {
            foreach ($arExcluidas as $arAux) {
                    $obTTGOBalanceteExtmmaa->setDado('cod_plano',$arAux['cod_plano']);
                    $obTTGOBalanceteExtmmaa->exclusao();
            }
        }
        if ( count( $arContas ) > 0 ) {
            foreach ($arContas as $arAux) {
                if ( count( $arAux ) > 0 ) {
                    foreach ($arAux as $arConta) {
                        $obTTGOBalanceteExtmmaa->setDado( 'cod_plano', $arConta['cod_plano'] );
                        $obTTGOBalanceteExtmmaa->setDado( 'tipo_lancamento', $arConta['tipo_lancamento'] );
                        $obTTGOBalanceteExtmmaa->setDado( 'categoria', $arConta['categoria'] );
                        if (Sessao::getExercicio() > '2011' && $arConta['sub_tipo_lancamento'] == '999') {
                           $obTTGOBalanceteExtmmaaTMP = new TTGOBalanceteExtmmaa();
                           $obTTGOBalanceteExtmmaaTMP->setDado( 'exercicio', Sessao::getExercicio() );
                           $obTTGOBalanceteExtmmaaTMP->setDado( 'cod_plano', $arConta['cod_plano'] );
                           $obTTGOBalanceteExtmmaaTMP->setDado( 'tipo_lancamento', $arConta['tipo_lancamento'] );
                           $obTTGOBalanceteExtmmaaTMP->setDado( 'categoria', $arConta['categoria'] );
                           // ">=5" indica que teve a conversao de "999-Outros" do subtipo em sequencial cfe lyout tcmgo
                           // Se não encontrar registro, inclui
                           $stFiltro = " AND sub_tipo_lancamento >= 5";
                           $obTTGOBalanceteExtmmaaTMP->recuperaRelacionamento( $rsContas, $stFiltro );
                           if ( $rsContas->getNumLinhas() <= 0 ) {
                               // Agora não seta conta, para poder pegar o max
                               // caso não encontre, usa o seq inicial cfe layout tce
                               $obTTGOBalanceteExtmmaaTMP2 = new TTGOBalanceteExtmmaa();
                               $obTTGOBalanceteExtmmaaTMP2->setDado( 'exercicio', Sessao::getExercicio() );
                               $obTTGOBalanceteExtmmaaTMP2->setDado( 'tipo_lancamento', $arConta['tipo_lancamento'] );
                               $obTTGOBalanceteExtmmaaTMP2->setDado( 'categoria', $arConta['categoria'] );
                               $obTTGOBalanceteExtmmaaTMP2->recuperaUltimoSequencialSubTipo( $rsContas, $stFiltro );
                               $maxSubTipo = 0;
                               if ( $rsContas->getNumLinhas() <= 0 ) {
                                   $maxSubTipo = 5;
                               } else {
                                   $maxSubTipo = (int) $rsContas->getCampo('max_sub_tipo');
                                   if ($maxSubTipo == 0 or empty($maxSubTipo)) {
                                       $maxSubTipo = 5;
                                   } else {
                                       $maxSubTipo = $maxSubTipo + 1;
                                   }
                               }
                               $obTTGOBalanceteExtmmaa->setDado( 'sub_tipo_lancamento', $maxSubTipo );
                               $obTTGOBalanceteExtmmaa->inclusao();
                           }
                        } else {
                            $obTTGOBalanceteExtmmaa->setDado( 'sub_tipo_lancamento', $arConta['sub_tipo_lancamento'] );
                            $obTTGOBalanceteExtmmaa->recuperaRelacionamento( $rsContas );
                            if ( $rsContas->getNumLinhas() <= 0 ) {
                                $obTTGOBalanceteExtmmaa->inclusao();
                            }
                        }
                    }
                }
            }
        }
            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
}

Sessao::encerraExcecao();

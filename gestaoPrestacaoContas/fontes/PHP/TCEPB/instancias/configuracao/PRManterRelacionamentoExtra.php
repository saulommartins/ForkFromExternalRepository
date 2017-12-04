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
    * PÃ¡gina de Processamento
    * Data de CriaÃ§Ã£o   : 25/01/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Revision: 59774 $
    $Name$
    $Author: jean $
    $Date: 2014-09-10 14:37:03 -0300 (Wed, 10 Sep 2014) $

    * Casos de uso: uc-06.03.00
*/

/*
$Log$
Revision 1.1  2007/05/11 15:10:56  hboaventura
Arquivos para geração do TCEPB

Revision 1.1  2007/04/27 18:31:00  hboaventura
Arquivos para geração do TCEPB

Revision 1.2  2007/04/23 15:41:02  rodrigo_sr
uc-06.03.00

Revision 1.1  2007/01/25 20:39:47  diego
Novos arquivos de exportação.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TTPB."TTPBPlanoAnaliticaRelacionamento.class.php");
include_once(TTPB."TTPBPlanoAnaliticaTipoRetencao.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterRelacionamentoExtra";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

Sessao::setTrataExcecao ( true );
$obMapeamento = new TTPBPlanoAnaliticaRelacionamento();
Sessao::getTransacao()->setMapeamento( $obMapeamento );
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obTTPBPlanoAnaliticaTipoRetencao = new TTPBPlanoAnaliticaTipoRetencao();

switch ($_REQUEST['stAcao']) {
  default:
    $obMapeamento->setDado('exercicio',Sessao::getExercicio());
    $obTTPBPlanoAnaliticaTipoRetencao->setDado('exercicio',Sessao::getExercicio());
    
    foreach ($_REQUEST as $stKey => $stValue) {
        if (strstr($stKey,'inCodigoReceita') ) {
            $arCodigo = explode('_',$stKey); //Formato: inCodigoReceita_1
            $obMapeamento->setDado('cod_plano',$arCodigo[1]);
            $obMapeamento->setDado('tipo','R');
            $obMapeamento->setDado('cod_relacionamento',$stValue);
            $obMapeamento->recuperaPorChave($rsRecordSet);
            
            if ($stValue != '') {
              if ($rsRecordSet->eof()) {
                  $obMapeamento->inclusao();
              } else {
                  $obMapeamento->alteracao();
              }
            } else {
              $obMapeamento->exclusao();
            }
        }
        
        if (strstr($stKey,'inCodigoDespesa') ) {
            $arCodigo = explode('_',$stKey); //Formato: inCodigoDespesa_1
            $obMapeamento->setDado('cod_plano',$arCodigo[1]);
            $obMapeamento->setDado('tipo','D');
            $obMapeamento->setDado('cod_relacionamento',$stValue);
            $obMapeamento->recuperaPorChave($rsRecordSet);
            if ($stValue != '') {
              if ($rsRecordSet->eof()) {
                  $obMapeamento->inclusao();
              } else {
                  $obMapeamento->alteracao();
              }
            } else {
              $obMapeamento->exclusao();
            }
        }
        
        if (strstr($stKey,'inRetencao')){
            $arCodigo = explode('_',$stKey);
            $obTTPBPlanoAnaliticaTipoRetencao->setDado('cod_plano',$arCodigo[1]);
            $obTTPBPlanoAnaliticaTipoRetencao->setDado('cod_tipo',$stValue);
            $obTTPBPlanoAnaliticaTipoRetencao->recuperaPorChave($rsRecordSet);
            if ($stValue != '') {
                if ($rsRecordSet->eof()) {
                    $obTTPBPlanoAnaliticaTipoRetencao->inclusao();
                } else {
                    $obTTPBPlanoAnaliticaTipoRetencao->alteracao();
                }
            } else {
              $obTTPBPlanoAnaliticaTipoRetencao->exclusao();
            }
        }
    }

    SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
  break;
}

Sessao::encerraExcecao();
?>

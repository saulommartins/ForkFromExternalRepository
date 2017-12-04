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
/*
    * Página Oculto para publicação do contrato
    * Data de Criação   : 16/10/2006

    * @author Analista: Cleisson da Silva Barboza
    * @author Desenvolvedor: Rodrigo

    * @ignore

    * Casos de uso : uc-02.03.32
*/

/*
$Log$
Revision 1.2  2007/09/06 20:07:35  luciano
Ticket#9094#

Revision 1.1  2007/08/10 14:31:28  luciano
movido de lugar

Revision 1.11  2007/07/09 21:02:12  luciano
Bug#9366#

Revision 1.10  2007/07/05 18:14:30  luciano
Bug#9366#,Bug#9368#

Revision 1.9  2007/07/05 16:17:35  luciano
Bug#9366#,Bug#9368#

Revision 1.8  2007/07/02 21:32:03  luciano
Bug#9402#

Revision 1.7  2007/06/25 19:07:22  luciano
Bug#9402#,Bug#9359#,Bug#9094#

Revision 1.6  2007/06/25 19:03:29  luciano
Bug#9402#

Revision 1.5  2007/04/24 15:58:54  luciano
Bug#9097#

Revision 1.4  2007/04/24 14:59:21  luciano
Bug#9097#

Revision 1.3  2007/03/08 18:58:30  luciano
Bug#8612#

Revision 1.2  2007/03/06 14:44:46  gelson
correção do caso de uso.

Revision 1.1  2006/10/18 18:57:28  rodrigo
Caso de Uso 02.03.32

*/

//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
//include mapeamentos
include_once( TEMP."TEmpenhoContraPartidaResponsavel.class.php"                                      );
include_once( TEMP."TEmpenhoResponsavelAdiantamento.class.php"                                       );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

if ($stAcao == 'incluir') {
    $stAcao = 'alterar';
}

$stPrograma = "ManterResponsaveisAdiantamento";
$pgForm     = "FM".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";

$obTEmpenhoResponsavel = new TEmpenhoResponsavelAdiantamento;
Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTEmpenhoResponsavel );
$boTransacao = Sessao::getTransacao();

switch ($stAcao) {

 case "alterar":
    $arValores = Sessao::read('arValores');
    if ( count( $arValores) > 0 ) {

        $stMensagem = "";
        $inCount = 1;
        $Pos = 0;

        $obTEmpenhoContraResponsavel = new TEmpenhoContraPartidaResponsavel;
        $obTEmpenhoResponsavel       = new TEmpenhoResponsavelAdiantamento;

        Sessao::getTransacao()->setMapeamento( $obTEmpenhoContraResponsavel );
        Sessao::getTransacao()->setMapeamento( $obTEmpenhoResponsavel );

        // Verifica se existe a contrapartida e insere ou altera
        $obTEmpenhoContraResponsavel->setDado('exercicio'           ,Sessao::getExercicio());
        $obTEmpenhoContraResponsavel->setDado('conta_contrapartida' ,$arValores[$Pos]['inCodContraPartida']);
        $obTEmpenhoContraResponsavel->setDado('prazo'               ,$arValores[$Pos]['inPrazo']);
        $obTEmpenhoContraResponsavel->recuperaPorChave( $rsContraPartida, $boTransacao );

        if ($rsContraPartida->getNumLinhas() < 1) {
            $obTEmpenhoContraResponsavel->inclusao($boTransacao);
        } else {
            $obTEmpenhoContraResponsavel->alteracao($boTransacao);
        }

        $stFiltro =" WHERE exercicio = '".Sessao::getExercicio()."' AND conta_contrapartida = ".$arValores[$Pos]['inCodContraPartida'];
        $obTEmpenhoResponsavel->recuperaTodos($rsJaForam, $stFiltro, '', $boTransacao);

        while ( !$rsJaForam->eof() ) {
            $stKeyDb = $rsJaForam->getCampo('exercicio').'-'.
                       $rsJaForam->getCampo('conta_contrapartida').'-'.
                       $rsJaForam->getCampo('numcgm');

            $arItensChave[$stKeyDb] = true;
            $rsJaForam->proximo();
        }

        foreach ($arValores as $key => $value) {

            $stKeyNew = Sessao::getExercicio().'-'.$value['inCodContraPartida'].'-'.$value['inCGM'];

            $obTEmpenhoResponsavel->setDado('exercicio'          ,Sessao::getExercicio());
            $obTEmpenhoResponsavel->setDado('numcgm'             ,$value['inCGM']);
            $obTEmpenhoResponsavel->setDado('conta_contrapartida',$value['inCodContraPartida']);
            $obTEmpenhoResponsavel->setDado('conta_lancamento'   ,$value['inCodContaLancamento']);
            $obTEmpenhoResponsavel->setDado('ativo'              ,($value['inCodSituacao']=="A")?true:false);

            if ( !isset( $arItensChave[$stKeyNew] ) ) {

                $arChave = explode('-',$stChave);
                $stFiltro = " WHERE exercicio = '".Sessao::getExercicio()."' AND conta_contrapartida = ".$value['inCodContraPartida']." AND conta_lancamento = ".$value['inCodContaLancamento']." ";
                $obTEmpenhoResponsavel->recuperaTodos($rsLocal,$stFiltro, '', $boTransacao);

                if ($rsLocal->getNumLinhas() > 0) {

                    $obTEmpenhoResponsavelDel = new TEmpenhoResponsavelAdiantamento;
                    $obTEmpenhoResponsavelDel->setDado( 'exercicio',Sessao::getExercicio() );
                    $obTEmpenhoResponsavelDel->setDado( 'numcgm', $rsLocal->getCampo('numcgm') );
                    $obTEmpenhoResponsavelDel->setDado( 'conta_contrapartida', $rsLocal->getCampo('conta_contrapartida') );
                    $obTEmpenhoResponsavelDel->exclusao($boTransacao);

                    $stKeyOld = Sessao::getExercicio().'-'.$rsLocal->getCampo('conta_contrapartida').'-'.$rsLocal->getCampo('numcgm');
                    unset( $arItensChave[$stKeyOld] );

                    $obTEmpenhoResponsavel->inclusao($boTransacao);
                    unset( $arItensChave[$stKeyNew] );

                } else {
                    $obTEmpenhoResponsavel->inclusao($boTransacao);
                }
            } else {
                $obTEmpenhoResponsavel->alteracao($boTransacao);
                unset( $arItensChave[$stKeyNew] );
            }
            $inCount++;
        }

        if (!$stMensagem) {
            if (is_array($arItensChave)) {
               foreach ($arItensChave as $stChave => $valor) {

                   $arChave = explode('-',$stChave);

                   $obTEmpenhoResponsavel->setDado( 'exercicio'                , $arChave[0] );
                   $obTEmpenhoResponsavel->setDado( 'conta_contrapartida'      , $arChave[1] );
                   $obTEmpenhoResponsavel->setDado( 'numcgm'                   , $arChave[2] );
                   $obTEmpenhoResponsavel->verificaExistenciaEmpenho( $rsVerificaEmpenho, '', '', $boTransacao );

                   if ($rsVerificaEmpenho->getNumLinhas() >= 0) {
                        $stMensagem = "Responsável ".$arChave[1]." está sendo utilizado pelo sistema.";
                   } else {
                        $obTEmpenhoResponsavel->setDado( 'exercicio'           , $arChave[0] );
                        $obTEmpenhoResponsavel->setDado( 'conta_contrapartida' , $arChave[1] );
                        $obTEmpenhoResponsavel->setDado( 'numcgm'              , $arChave[2] );
                        $obTEmpenhoResponsavel->exclusao($boTransacao);
                   }
                }
            }
        }

    } else {
        $stMensagem = "Deve existir pelo menos um responsável na lista.";
    }

    if (!$stMensagem) {
        if ($_REQUEST['stAcao'] == 'incluir') {
            SistemaLegado::alertaAviso($pgForm.'?'.Sessao::getId()."&stAcao=incluir",'Contrapartida '.$arValores[$Pos]['inCodContraPartida'].'/'.Sessao::getExercicio(),"incluir", "aviso", Sessao::getId(),"");
        } else {
            SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=$stAcao",'Contrapartida '.$arValores[$Pos]['inCodContraPartida'].'/'.Sessao::getExercicio(),"alterar", "aviso", Sessao::getId(),"");
        }
    } else {
        SistemaLegado::exibeAviso(urlencode($stMensagem), "n_alterar", "erro" );
    }

 break;

 case 'excluir':

     $obTEmpenhoContraResponsavel = new TEmpenhoContraPartidaResponsavel;
     $obTEmpenhoResponsavel       = new TEmpenhoResponsavelAdiantamento;

     $obTEmpenhoResponsavel->setDado('exercicio',Sessao::getExercicio());
     $obTEmpenhoResponsavel->setDado('numcgm',$_REQUEST['numcgm']);
     $obTEmpenhoResponsavel->setDado('conta_contrapartida',$_REQUEST['inCodContraPartida']);
     $obTEmpenhoResponsavel->verificaExistenciaEmpenho($rsVerificaEmpenho);

     if ($rsVerificaEmpenho->getNumLinhas() >= 0) {
         SistemaLegado::exibeAviso("Responsável está sendo utilizado pelo sistema",'n_excluir','erro');
         SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=$stAcao","&nbsp;","n_excluir", "erro", Sessao::getId(),"");
     } else {
         $obTEmpenhoResponsavel->setDado('exercicio',Sessao::getExercicio());
         $obTEmpenhoResponsavel->setDado('conta_contrapartida',$_REQUEST['inCodContraPartida']);
         $obTEmpenhoResponsavel->setDado('numcgm',$_REQUEST['numcgm']);
         $obTEmpenhoResponsavel->exclusao();

         $obTEmpenhoResponsavel->setDado('exercicio',Sessao::getExercicio());
         $obTEmpenhoResponsavel->setDado('conta_contrapartida',$_REQUEST['inCodContraPartida']);
         $obTEmpenhoResponsavel->recuperaTodos($rsResponsaveis);

         if ($rsResponsaveis->getNumLinhas() <= 0) {
           $obTEmpenhoContraResponsavel->setDado('exercicio',Sessao::getExercicio());
           $obTEmpenhoContraResponsavel->setDado('conta_contrapartida',$_REQUEST['inCodContraPartida']);
           $obTEmpenhoContraResponsavel->exclusao();
         }

         SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=$stAcao"," &nbsp; ","excluir", "aviso", Sessao::getId(),"");
     }

 break;

}
Sessao::encerraExcecao();

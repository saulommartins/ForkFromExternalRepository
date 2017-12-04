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
  * Pagina Oculta de Relacionamento de Conta - Entidade - TCE-RS
  * Data de Criação: 26/05/2008

  * @author Desenvolvedor: Diogo Zarpelon
  *
  * $Id: $
  *
  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GPC_TCERS_MAPEAMENTO."TExportacaoTCERSPlanoContaEntidade.class.php"                             );
include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php"                                         );

function montaListaPlanoContaEntidade($arRecordSet , $boExecuta = true)
{

    $stPrograma = "ManterContaEntidade";
    $pgOcul     = "OC".$stPrograma.".php";

    $rsPlanoContaEntidade = new RecordSet;
    $rsPlanoContaEntidade->preenche( $arRecordSet );

    $table = new Table();
    $table->setRecordset( $rsPlanoContaEntidade );
    $table->setSummary("Relacionamento de Conta com Entidade");

    //$table->setConditional( true , "#efefef" );

    $table->Head->addCabecalho( '&nbsp;'   ,  3 );
    $table->Head->addCabecalho( 'Entidade' , 32 );
    $table->Head->addCabecalho( 'Conta'    , 60 );
    $table->Head->addCabecalho( '&nbsp;'   ,  5 );

    $stTitle = "";

    $table->Body->addCampo( "[cod_entidade] - [nom_cgm]"     , "E", $stTitle );
    $table->Body->addCampo( "[cod_estrutural] - [nom_conta]" , "E", $stTitle );

    $table->Body->addAcao( 'excluir' , 'excluirPlanoContaEntidade(%s)' , array( 'id' ) );

    $table->montaHTML();
    $stHTML = $table->getHtml();

    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    if ($boExecuta) {
        $js ="document.getElementById('spnListaPlanoContaEntidade').innerHTML = '".$stHTML."';";

        return $js;
    } else {
        return $stHTML;
    }
}

$arValores = Sessao::read('arValores');
switch ($_REQUEST['stCtrl']) {
    case "montaListaPlanoContaEntidade" :
        $arValoresPlano = ( is_array($arValores) ) ? $arValores : array();
        echo montaListaPlanoContaEntidade( $arValoresPlano );
    break;

    case "carregaListaPlanoContaEntidade" :
        $obTContaEntidade = new TExportacaoTCERSPlanoContaEntidade();
        $rsRecordSet = new RecordSet;
        $obTContaEntidade->recuperaRelacionamento($rsRecordSet);

        if (!($rsRecordSet->EOF())) {
            while (!($rsRecordSet->EOF())) {
                $inCount = sizeof($arValores);

                $arValores[$inCount][ 'id'             ] = $inCount + 1;
                $arValores[$inCount][ 'exercicio'      ] = $rsRecordSet->getCampo( 'exercicio'    );
                $arValores[$inCount][ 'cod_entidade'   ] = $rsRecordSet->getCampo( 'cod_entidade' );
                $arValores[$inCount][ 'nom_cgm'        ] = $rsRecordSet->getCampo( 'nom_cgm' );
                $arValores[$inCount][ 'cod_estrutural' ] = $rsRecordSet->getCampo( 'cod_estrutural' );
                $arValores[$inCount][ 'cod_conta'      ] = $rsRecordSet->getCampo( 'cod_conta'    );
                $arValores[$inCount][ 'nom_conta'      ] = $rsRecordSet->getCampo( 'nom_conta' );

                $rsRecordSet->Proximo();
            }
        }
        Sessao::write('arValores', $arValores);
        echo $js.montaListaPlanoContaEntidade($arValores);
    break;

    case "inserirPlanoContaEntidade" :

        if (!empty($_GET['inCodEntidade']) && !empty($_GET['stCodReduzido'])) {

            $arValores = Sessao::read('arValores');
            $obTPlanoConta = new TContabilidadePlanoConta();
            $obTPlanoConta->recuperaContaPlanoAnalitica($rsPlanoConta, " AND pa.exercicio='".Sessao::getExercicio()."' AND pa.cod_plano='".$_GET['stCodReduzido']."'");

            // Verifica se o registro já não existe na tabela para evitar duplicidade.
            $obTContaEntidade = new TExportacaoTCERSPlanoContaEntidade();
            $rsRecordSet = new RecordSet;
            $obTContaEntidade->recuperaTodos($rsRecordSet, " WHERE exercicio = '".Sessao::getExercicio()."' AND cod_conta = ".$rsPlanoConta->getCampo('cod_conta'));
            $obDuplicado = ($rsRecordSet->getNumLinhas() > 0) ? true : false;

            $obTContaEntidade->setDado('inCodEntidade' , $_GET['inCodEntidade']);
            $obTContaEntidade->setDado('exercicio', Sessao::getExercicio());
            $obTContaEntidade->recuperaNomeEntidade($rsEntidade);

            // Verifica se o registro já não existe no array para evitar duplicidade.
            if (is_array($arValores)) {
                foreach ($arValores as $k=>$v) {
                    if ($rsPlanoConta->getCampo('cod_conta') == $arValores[$k]['cod_conta']) {
                      if (Sessao::getExercicio() == $arValores[$k]['exercicio']) {
                        $obDuplicado = true;
                        break;
                      }
                    }
                }
            }

            if ($obDuplicado)
                echo "alertaAviso('Essa conta já está vinculada!','form','erro','".Sessao::getId()."');";
            else{
                $inCount = sizeof($arValores);

                $arValores[$inCount][ 'id'             ] = $inCount + 1;
                $arValores[$inCount][ 'exercicio'      ] = Sessao::getExercicio();
                $arValores[$inCount][ 'cod_entidade'   ] = $_GET[ 'inCodEntidade' ];
                $arValores[$inCount][ 'nom_cgm'        ] = $rsEntidade->getCampo( 'nom_cgm' );
                $arValores[$inCount][ 'cod_estrutural' ] = $rsPlanoConta->getCampo( 'cod_estrutural' );
                $arValores[$inCount][ 'cod_conta'      ] = $rsPlanoConta->getCampo( 'cod_conta'    );
                $arValores[$inCount][ 'nom_conta'      ] = $rsPlanoConta->getCampo( 'nom_conta' );

                Sessao::write('arValores', $arValores);
            }
        } else {
            echo "alertaAviso('Preencha os campos obrigatórios destacados com *','form','erro','".Sessao::getId()."');";
        }
        Sessao::write('arValores', $arValores);
        echo $js .= "LimparPlanoContaEntidade(); ";
        echo $js.montaListaPlanoContaEntidade($arValores);
    break;

    case 'excluirPlanoContaEntidade':
        if (!empty($_GET['id'])) {
            $arValores = Sessao::read('arValores');
            $arValoresTMP = array();
            $inCount = 0;
            foreach ($arValores as $arValoresAUX) {
                if ($arValoresAUX['id'] != $_GET['id']) {
                    $arValoresTMP[$inCount][ 'id'             ] = $inCount + 1;
                    $arValoresTMP[$inCount][ 'exercicio'      ] = $arValoresAUX[ 'exercicio' ]   ;
                    $arValoresTMP[$inCount][ 'cod_entidade'   ] = $arValoresAUX[ 'cod_entidade' ];
                    $arValoresTMP[$inCount][ 'nom_cgm'        ] = $arValoresAUX[ 'nom_cgm' ];
                    $arValoresTMP[$inCount][ 'cod_estrutural' ] = $arValoresAUX[ 'cod_estrutural' ];
                    $arValoresTMP[$inCount][ 'cod_conta'      ] = $arValoresAUX[ 'cod_conta' ];
                    $arValoresTMP[$inCount][ 'nom_conta'      ] = $arValoresAUX[ 'nom_conta' ];
                    $inCount++;
                }
            }
            Sessao::write('arValores', $arValoresTMP);
            echo $js.montaListaPlanoContaEntidade($arValoresTMP);
        }
    break;

}

?>

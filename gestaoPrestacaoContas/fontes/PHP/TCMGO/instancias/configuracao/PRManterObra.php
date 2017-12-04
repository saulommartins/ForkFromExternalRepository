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
    * Página de Processamento
    * Data de Criação   : 16/04/2007

    * @author Analista      Tonismar Bernardo
    * @author Desenvolvedor

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: PRManterObra.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( TTGO.'TTGOObras.class.php' );
include_once( TTGO.'TTGOObrasEmpenho.class.php' );

//Define o nome dos arquivos PHP
$stPrograma = "ManterObra";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$inGrauLatitude = str_pad($_REQUEST['inGrauLatitude'], 2, '0', STR_PAD_LEFT);
$inMinutoLatitude = str_pad($_REQUEST['inMinutoLatitude'], 2, '0', STR_PAD_LEFT);
$arSegundoLatitude = explode('.', $_REQUEST['inSegundoLatitude']);
$inSegundoLatitude  = str_pad($arSegundoLatitude[0], 2, '0', STR_PAD_LEFT);
$inSegundoLatitude .= '.'.str_pad($arSegundoLatitude[1], 2, '0');

$inGrauLongitude = str_pad($_REQUEST['inGrauLongitude'], 2, '0', STR_PAD_LEFT);
$inMinutoLongitude = str_pad($_REQUEST['inMinutoLongitude'], 2, '0', STR_PAD_LEFT);
$arSegundoLongitude = explode('.', $_REQUEST['inSegundoLongitude']);
$inSegundoLongitude  = str_pad($arSegundoLongitude[0], 2, '0', STR_PAD_LEFT);
$inSegundoLongitude .= '.'.str_pad($arSegundoLongitude[1], 2, '0');

$arUnidadeMedida = explode('-', $_REQUEST['inCodUnidadeMedida']);

$stAcao = $request->get('stAcao');
$arEmpenhos = Sessao::read('arEmpenhos');

switch ($_REQUEST['stAcao']) {
    case 'incluir' :

        Sessao::setTrataExcecao ( true );

        $rsRecordSet = new RecordSet;
        $rsRecordSet->preenche(Sessao::read('arEmpenhos'));

        $obTTGOObras = new TTGOObras;
        $stFiltro .= " WHERE cod_obra = ".$_POST['inCodObra'];
        $stFiltro .= "   AND ano_obra = ".$_POST['stExercicio'];
        $obTTGOObras->recuperaTodos($rsObras,$stFiltro);

        if ( $rsObras->getNumLinhas() < 0 ) {

            $obTTGOObras->setDado('cod_obra',$_POST['inCodObra'  ] );
            $obTTGOObras->setDado('ano_obra',$_POST['stExercicio'] );
            $obTTGOObras->setDado('especificacao', $_POST['stDescricao'] );
            $obTTGOObras->setDado('grau_latitude', $inGrauLatitude);
            $obTTGOObras->setDado('minuto_latitude', $inMinutoLatitude);
            $obTTGOObras->setDado('segundo_latitude', $inSegundoLatitude);
            $obTTGOObras->setDado('grau_longitude', $inGrauLongitude);
            $obTTGOObras->setDado('minuto_longitude', $inMinutoLongitude);
            $obTTGOObras->setDado('segundo_longitude', $inSegundoLongitude);
            $obTTGOObras->setDado('cod_unidade', $arUnidadeMedida[0]);
            $obTTGOObras->setDado('cod_grandeza', $arUnidadeMedida[1]);
            $obTTGOObras->setDado('quantidade', $_REQUEST['inQuantidade']);
            $obTTGOObras->setDado('endereco', $_REQUEST['stEndereco']);
            $obTTGOObras->setDado('bairro', $_REQUEST['stBairro']);
            $obTTGOObras->setDado('fiscal', $_REQUEST['stFiscal']);

            $obTTGOObras->inclusao();

            while ( !$rsRecordSet->eof() ) {
                $obTTGOObrasEmpenho = new TTGOObrasEmpenho;
                $obTTGOObrasEmpenho->setDado( 'cod_obra' ,  $_POST['inCodObra'  ]     );
                $obTTGOObrasEmpenho->setDado( 'ano_obra' ,  $_POST['stExercicio'   ]  );
                $obTTGOObrasEmpenho->setDado( 'cod_empenho'  , $rsRecordSet->getCampo('cod_empenho')  );
                $obTTGOObrasEmpenho->setDado( 'cod_entidade' , $rsRecordSet->getCampo('cod_entidade') );
                $obTTGOObrasEmpenho->setDado( 'exercicio'    , $rsRecordSet->getCampo('exercicio')    );
                $obTTGOObrasEmpenho->inclusao();
                $rsRecordSet->proximo();
            }
            sistemaLegado::alertaAviso($pgForm , $_POST['inCodObra'] .'/'. $_POST['stExercicio']  ,"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso( 'Esta obra já foi cadastrada.',"n_incluir","erro" );
        }

        Sessao::encerraExcecao();

    break;

    case 'alterar':
        Sessao::setTrataExcecao ( true );

        $obTTGOObras = new TTGOObras;
        $obTTGOObras->setDado('cod_obra'         , $_POST['inCodObra'  ]);
        $obTTGOObras->setDado('ano_obra'         , $_POST['stExercicio']);
        $obTTGOObras->setDado('especificacao'    , $_POST['stDescricao']);
        $obTTGOObras->setDado('grau_latitude'    , $inGrauLatitude);
        $obTTGOObras->setDado('minuto_latitude'  , $inMinutoLatitude);
        $obTTGOObras->setDado('segundo_latitude' , $inSegundoLatitude);
        $obTTGOObras->setDado('grau_longitude'   , $inGrauLongitude);
        $obTTGOObras->setDado('minuto_longitude' , $inMinutoLongitude);
        $obTTGOObras->setDado('segundo_longitude', $inSegundoLongitude);
        $obTTGOObras->setDado('cod_unidade'      , $arUnidadeMedida[0]);
        $obTTGOObras->setDado('cod_grandeza'     , $arUnidadeMedida[1]);
        $obTTGOObras->setDado('quantidade'       , $_REQUEST['inQuantidade']);
        $obTTGOObras->setDado('endereco'         , $_REQUEST['stEndereco']);
        $obTTGOObras->setDado('bairro'           , $_REQUEST['stBairro']);
        $obTTGOObras->setDado('fiscal'           , $_REQUEST['stFiscal']);

        $obTTGOObras->alteracao();

        $obTTGOObrasEmpenho = new TTGOObrasEmpenho;
        $obTTGOObrasEmpenho->setDado( 'cod_obra' ,  $_POST['inCodObra'  ]  );
        $obTTGOObrasEmpenho->setDado( 'ano_obra' ,  $_POST['stExercicio'   ]  );
        $obTTGOObrasEmpenho->exclusao();
        foreach ($arEmpenhos as $registro) {
           $obTTGOObrasEmpenho->setDado( 'cod_empenho'  , $registro['cod_empenho' ] );
           $obTTGOObrasEmpenho->setDado( 'cod_entidade' , $registro['cod_entidade'] );
           $obTTGOObrasEmpenho->setDado( 'exercicio'    , Sessao::getExercicio()        );
           $obTTGOObrasEmpenho->inclusao();
        }

        sistemaLegado::alertaAviso($pgList , $_POST['inCodObra'  ] .'/'. $_POST['stExercicio']  ,"alterar","aviso", Sessao::getId(), "../");
        Sessao::encerraExcecao();

    break;

    case 'excluir':

        Sessao::setTrataExcecao ( true );
        $obTTGOObrasEmpenho = new TTGOObrasEmpenho;
        $obTTGOObrasEmpenho->setDado( 'cod_obra' ,  $_GET['cod_obra'  ]  );
        $obTTGOObrasEmpenho->setDado( 'ano_obra' ,  $_GET['ano_obra'  ]  );
        $obTTGOObrasEmpenho->exclusao();

        $obTTGOObras = new TTGOObras;
        $obTTGOObras->setDado ( 'cod_obra'      , $_GET['cod_obra'  ] );
        $obTTGOObras->setDado ( 'ano_obra'      , $_GET['ano_obra'  ] );
        $obTTGOObras->exclusao();
        sistemaLegado::alertaAviso($pgList , $_GET['cod_obra'  ] .'/'. $_GET['ano_obra']  ,"excluir","aviso", Sessao::getId(), "../");
        Sessao::encerraExcecao();

    break;

}

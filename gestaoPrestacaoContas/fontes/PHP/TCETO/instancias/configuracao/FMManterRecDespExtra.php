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
    * Pacote de configuração do TCETO - Formulário Configurar Receita/Despesa Extra
    * Data de Criação   : 07/11/2014

    * @author Analista: Silvia Martins Silva
    * @author Desenvolvedor: Michel Teixeira
    * $Id: FMManterRecDespExtra.php 60671 2014-11-07 13:27:44Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCETO_MAPEAMENTO.'TTTOPlanoAnaliticaClassificacao.class.php';

$stPrograma = "ManterRecDespExtra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$stLocation = $pgList . "?". Sessao::getId() . "&stAcao=" . $stAcao;

if ($inCodigo)
    $stLocation .= "&inCodigo=$inCodigo";

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obLabel = new Label();
$obLabel->setValue("Os arquivos serão agrupados pelo código informado para as entidades");

//Lista de códigos cadastrados para cada entidade
$obTTTOPlanoAnaliticaClassificacao = new TTTOPlanoAnaliticaClassificacao();
$stFiltro = "   AND  plano_conta.exercicio = '".Sessao::getExercicio()."'
                AND (plano_conta.cod_estrutural LIKE '1.1.2%' OR plano_conta.cod_estrutural LIKE '2.1%' OR plano_conta.cod_estrutural LIKE '1.1.3%')

                AND EXISTS (    SELECT  conta_debito.cod_plano
                                  FROM  contabilidade.conta_debito
                                 WHERE  plano_analitica.cod_plano = conta_debito.cod_plano
                                   AND  plano_analitica.exercicio = conta_debito.exercicio

                                 UNION

                                SELECT  conta_credito.cod_plano
                                  FROM  contabilidade.conta_credito
                                 WHERE  plano_analitica.cod_plano = conta_credito.cod_plano
                                   AND  plano_analitica.exercicio = conta_credito.exercicio

                                 LIMIT  1
                           ) 
            ";
            
$stOrder = "    ORDER BY    plano_conta.cod_estrutural   ";
$obTTTOPlanoAnaliticaClassificacao->recuperaContaAnalitica( $rsContas, $stFiltro, $stOrder );

$obLista = new Lista();
$obLista->setMostraPaginacao(false);
$obLista->setTitulo('Lista de Códigos de cada Entidade');
$obLista->setRecordSet($rsContas);

//Cabeçalhos
$obLista->addCabecalho(''                   , 5 );
$obLista->addCabecalho('Reduzido'           , 4 );
$obLista->addCabecalho('Estrutural'         , 10);
$obLista->addCabecalho('Descrição da Conta' , 35);
$obLista->addCabecalho('Classificação'      , 10);

//Dados
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('cod_plano');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('cod_estrutural');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('nom_conta');
$obLista->commitDado();

$obCmbCodigoReceita = new Select();
$obCmbCodigoReceita->setName  ( 'inCodigoClassificacao_[cod_plano]'     );
$obCmbCodigoReceita->setId    ( 'inCodigoClassificacao'                 );
$obCmbCodigoReceita->addOption( ''  , 'Selecione'                       );
$obCmbCodigoReceita->addOption( '01', 'Restos a Pagar'                  );
$obCmbCodigoReceita->addOption( '02', 'Serviços da Dívida'              );
$obCmbCodigoReceita->addOption( '03', 'Depósitos'                       );
$obCmbCodigoReceita->addOption( '04', 'Convênios'                       );
$obCmbCodigoReceita->addOption( '05', 'Débitos da Tesouraria'           );
$obCmbCodigoReceita->addOption( '06', 'Outras Operações (Realizável)'   );
$obCmbCodigoReceita->addOption( '07', 'Interferências Financeiras'      );
$obCmbCodigoReceita->setValue ( 'cod_classificacao'                     );    

$obLista->addDadoComponente( $obCmbCodigoReceita , false );
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo( "valor" );
$obLista->commitDadoComponente();

$obSpnCodigos = new Span();
$obSpnCodigos->setId('spnCodigos');
$obLista->montaHTML();
$obSpnCodigos->setValue($obLista->getHTML());

//DEFINICAO DOS COMPONENTES
$obFormulario = new Formulario();
$obFormulario->addForm  ($obForm);

$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addTitulo( "Parâmetros por Entidade" );
$obFormulario->addSpan  ($obSpnCodigos);

$obFormulario->OK(true);
$obFormulario->show();

SistemaLegado::LiberaFrames(true, false);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

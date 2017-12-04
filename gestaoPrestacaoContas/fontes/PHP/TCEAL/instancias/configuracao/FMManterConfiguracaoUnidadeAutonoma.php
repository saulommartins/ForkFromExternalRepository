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
    * Pacote de configuração do TCEAL
    * Data de Criação   : 08/10/2013

    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_FW_COMPONENTES . '/Table/Table.class.php');
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php");
include_once(CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALConfiguracaoUnidadeAutonoma.class.php');

$stPrograma = 'ManterConfiguracaoUnidadeAutonoma';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJs   = 'JS'.$stPrograma.'.js';

$stAcao = $request->get('stAcao');

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//Lista de códigos cadastrados para cada entidade
$obPersistente = new TTCEALConfiguracaoUnidadeAutonoma();
$obPersistente->setDado('parametro','tceal_configuracao');
$obPersistente->recuperaCodigos($rsEntidades,''," ORDER BY entidade.cod_entidade");

$obLista = new Lista();
$obLista->setMostraPaginacao(false);
$obLista->setTitulo('Lista de Códigos de cada Entidade');
$obLista->setRecordSet($rsEntidades);

//Cabeçalhos
$obLista->addCabecalho('', 5);
$obLista->addCabecalho('Entidade', 55);

//Dados
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('[cod_entidade] - [nom_cgm]');
$obLista->commitDado();

$obTxtCodigo = new TextBox();
$obTxtCodigo->setName           ('inCodigo_[cod_entidade]');
$obTxtCodigo->setValue          ('[valor]');
$obTxtCodigo->setSize           ( 8 );
$obTxtCodigo->setMaxLength      ( 4 );
$obTxtCodigo->setInteiro        ( true  );

$obLista->addCabecalho('Unidade Gestora', 10);
$obLista->addDadoComponente( $obTxtCodigo , false);
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo( "valor" );
$obLista->commitDadoComponente();

$obSpnCodigos = new Span();
$obSpnCodigos->setId('spnCodigos');
$obLista->montaHTML();
$obSpnCodigos->setValue($obLista->getHTML());

//DEFINICAO DOS COMPONENTES
$obFormulario = new Formulario();
$obFormulario->addForm($obForm);

$obFormulario->addHidden($obHdnAcao);
$obFormulario->addTitulo( "Parâmetros por Entidade" );
$obFormulario->addSpan  ($obSpnCodigos);

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>

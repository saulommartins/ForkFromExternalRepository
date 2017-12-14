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
    * Formulário de Configuração da esfera do convênio
    * Data de Criação   : 11/04/2011

    * @author: Eduardo Paculski Schitz

    * @ignore
    * $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_FW_COMPONENTES . '/Table/Table.class.php';
require_once CAM_GPC_TCEAM_MAPEAMENTO.'TTCEAMEsferaConvenio.class.php';

$stPrograma = 'VincularEsferaConvenio';
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
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao);

$obConvenio = new TTCEAMEsferaConvenio;
$obConvenio->recuperaEsferaConvenio($rsConvenios);

$arEsfera = array(
      array('esfera' => 'E', 'descricao' => 'Estadual')
    , array('esfera' => 'F', 'descricao' => 'Federal')
    , array('esfera' => 'M', 'descricao' => 'Municipal')
    , array('esfera' => 'G', 'descricao' => 'ONGs')
    , array('esfera' => 'O', 'descricao' => 'Outros')
);

$rsEsfera = new RecordSet;
$rsEsfera->preenche($arEsfera);

//cria um select com as contas do Elenco de contas do TCE
$obCmbEsfera = new Select;
$obCmbEsfera->setId        ('slEsfera_[num_convenio]_[exercicio]');
$obCmbEsfera->setName      ('slEsfera_[num_convenio]_[exercicio]');
$obCmbEsfera->setCampoId   ('[esfera]');
$obCmbEsfera->setCampoDesc ('[descricao]');
$obCmbEsfera->addOption    ('','Selecione');
$obCmbEsfera->preencheCombo($rsEsfera);
$obCmbEsfera->setValue     ('[esfera]');

$obLista = new Lista();
$obLista->setMostraPaginacao(false);
$obLista->setTitulo('Lista de Convênios');
$obLista->setRecordSet($rsConvenios);

//Cabeçalhos
$obLista->addCabecalho('', 5);
$obLista->addCabecalho('Número Convênio', 10);
$obLista->addCabecalho('Objeto', 80);
$obLista->addCabecalho('Esfera', 10);

//Dados
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('[num_convenio]');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('[descricao_objeto]');
$obLista->commitDado();

$obLista->addDadoComponente($obCmbEsfera, false);
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo("esfera");
$obLista->commitDadoComponente();

$obSpnLista = new Span();
$obSpnLista->setId('spnCodigos');
$obLista->montaHTML();
$obSpnLista->setValue($obLista->getHTML());

$obFormulario = new Formulario();
$obFormulario->addForm  ($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addSpan  ($obSpnLista);
$obFormulario->Ok();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>

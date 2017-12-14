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
* Arquivo de instância para manutenção de normas
* Data de Criação: 04/09/2005

* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

$Revision: 15582 $
$Name$
$Author: cassiano $
$Date: 2006-09-18 08:38:09 -0300 (Seg, 18 Set 2006) $

Casos de uso: uc-01.06.95
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$stCtrl = $_REQUEST['stCtrl'];

//pega os dados da acao
$arAcaoSessao = Sessao::read('acaoSessao');

switch ($stCtrl) {
    case 'incluirAcao':
        if (is_array($arAcaoSessao)) {
            foreach ($arAcaoSessao as $arAcao) {
                if ($arAcao['cod_acao'] == $_REQUEST['inCodigoAcao']) {
                    echo 'alertaAviso("Ação '.$_REQUEST['inCodigoAcao'].' já foi infromada!","n_incluir","erro","'.Sessao::getId().'");';
                    exit();
                }
            }
        }
        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoAcao.class.php");
        $stFiltro = ' AND A.cod_acao='.$_REQUEST['inCodigoAcao'];
        $obTAdministracaoAcao = new TAdministracaoAcao();
        $obErro = $obTAdministracaoAcao->recuperaRelacionamento($rsAcao, $stFiltro );
        if ( $obErro->ocorreu() ) {
            echo $obErro->getDescricao();
        }
        $countAcao = count($arAcaoSessao);
        $arAcao = Array(    'nom_gestao' => $rsAcao->getCampo('nom_gestao'),
                            'nom_modulo' => $rsAcao->getCampo('nom_modulo'),
                            'nom_funcionalidade' => $rsAcao->getCampo('nom_funcionalidade'),
                            'nom_acao' => $rsAcao->getCampo('nom_acao'),
                            'cod_acao' => $rsAcao->getCampo('cod_acao')
                        );
        $arAcaoSessao[$countAcao] = $arAcao;
    break;

    case 'excluirAcao':
        $arAcaoTmp = array();
        foreach ($arAcaoSessao as $arAcao) {
            if ($arAcao['cod_acao'] != $_REQUEST['inCodigoAcao']) {
                $arAcaoTmp[] = $arAcao;
            }
        }
        $arAcaoSessao = $arAcaoTmp;
    break;
}

Sessao::write('acaoSessao',$arAcaoSessao);

$rsAcao = new RecordSet();
$rsAcao->preenche($arAcaoSessao);

$obLista = new Lista();
$obLista->setMostraPaginacao(false);
$obLista->setTitulo('Lista de Ações Selecionadas');
$obLista->setRecordSet($rsAcao);
$obLista->addCabecalho('', 5);
$obLista->addCabecalho('Gestão', 15);
$obLista->addCabecalho('Modulo', 15);
$obLista->addCabecalho('Funcionalidade', 15);
$obLista->addCabecalho('Ação', 15);
$obLista->addCabecalho('', 5);

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('nom_gestao');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('nom_modulo');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('nom_funcionalidade');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('[cod_acao]-[nom_acao]');
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao('Excluir');
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:excluirAcao()");
$obLista->ultimaAcao->addCampo("1","cod_acao");
$obLista->commitAcao();

$obLista->montaHTML();

$stHtml = $obLista->getHTML();

$stHtml = str_replace("\n","",$stHtml);
$stHtml = str_replace("  ","",$stHtml);
$stHtml = str_replace("'","\\'",$stHtml);
$stHtml = str_replace(chr(13),"",$stHtml);
$stHtml = str_replace(chr(13).chr(10),"",$stHtml);
$stJs  = 'd.getElementById("spnListaAcao").innerHTML = \''.$stHtml.'\';';
$stJs .= 'f.inCodigoAcao.value="";';
$stJs .= 'd.getElementById("stNomeAcao").innerHTML = \'&nbsp;\';';
echo $stJs;
?>

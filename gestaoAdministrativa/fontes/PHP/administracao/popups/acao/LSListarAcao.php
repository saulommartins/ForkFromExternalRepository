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
* Data de Criação: 06/09/2005

* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

$Revision: 15641 $
$Name$
$Author: cassiano $
$Date: 2006-09-18 13:25:02 -0300 (Seg, 18 Set 2006) $

Casos de uso: uc-01.03.93
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoGestao.class.php");
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoModulo.class.php");
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoFuncionalidade.class.php");
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoAcao.class.php");

$stNomeForm = $_REQUEST['nomForm'];
$stCampoNom = $_REQUEST['campoNom'];
$stCampoNum = $_REQUEST['campoNum'];
include_once 'JSListarAcao.js';

$obTAdministracaoGestao         = new TAdministracaoGestao();
$obTAdministracaoModulo	        = new TModulo();
$obTAdministracaoFuncionalidade = new TAdministracaoFuncionalidade();
$obTAdministracaoAcao           = new TAdministracaoAcao();

$obTAdministracaoGestao->recuperaTodos( $rsGestao,'','cod_gestao,ordem');
$obTAdministracaoModulo->recuperaTodos($rsModulo,' WHERE cod_modulo > 0 ','cod_gestao,ordem,cod_modulo');
$obTAdministracaoFuncionalidade->recuperaTodos($rsFuncionalidade, '', 'cod_modulo,ordem,cod_funcionalidade');
$obTAdministracaoAcao->recuperaTodos($rsAcao,'','cod_funcionalidade,ordem,cod_acao');

$arAcao             = Array();
$arModulo           = Array();
$arFuncionalidade   = Array();
$arAcao             = Array();

while ( !$rsGestao->eof() ) {
    $arGestao[$rsGestao->getCampo('cod_gestao')] = $rsGestao->getCampo('nom_gestao');
    $rsGestao->proximo();
}
while ( !$rsModulo->eof() ) {
    $arModulo[$rsModulo->getCampo('cod_gestao')][$rsModulo->getCampo('cod_modulo')]=$rsModulo->getCampo('nom_modulo');
    $rsModulo->proximo();
}
while ( !$rsFuncionalidade->eof()) {
    $arFuncionalidade[$rsFuncionalidade->getCampo('cod_modulo')][$rsFuncionalidade->getCampo('cod_funcionalidade')]=$rsFuncionalidade->getCampo('nom_funcionalidade');
    $rsFuncionalidade->proximo();
}
while ( !$rsAcao->eof()) {
    $arTmp = Array( 'cod_acao'=>$rsAcao->getCampo('cod_acao'),
                    'nom_acao'=>$rsAcao->getCampo('nom_acao')
                  );
    $arAcao[$rsAcao->getCampo('cod_funcionalidade')][]= $arTmp;
    $rsAcao->proximo();
}

//BOTAO FECHAR
$obBtnFechar = new Button();
$obBtnFechar->setName('btnFechar');
$obBtnFechar->setValue('Fechar');
$obBtnFechar->obEvento->setOnClick('javascript: self.close();');

$obFormularioGestao = new FormularioAbas();
//LOOP PARA MONTAR AS ABAS DAS GESTOES
foreach ($arGestao as $inCodigoGestao => $stNomeGestao) {
    $obFormularioGestao->addAba($stNomeGestao);
    $obFormularioGestao->addTitulo('Modulos da Gestão '.$stNomeGestao);
    $obFormularioModulo = new FormularioAbas();

    //LOOP PARA MONTAR AS ABAS DOS MODULOS
    foreach ($arModulo[$inCodigoGestao] as $inCodigoModulo => $stNomeModulo) {
        if ( !is_array($arFuncionalidade[$inCodigoModulo]) ) {
            continue;
        }
        $obFormularioModulo->addAba($stNomeModulo);
        $obFormularioModulo->addTitulo('Módulo '.$stNomeModulo);
        //LOOP PARA LISTAR AS FUNCIONALIDADES
        foreach ($arFuncionalidade[$inCodigoModulo] as $inCodigoFuncionalidade => $stNomeFuncionalidade) {
            if (!is_array($arAcao[$inCodigoFuncionalidade])) {
                continue;
            }
            $rsAcao = new RecordSet();
            $rsAcao->preenche($arAcao[$inCodigoFuncionalidade]);
            $obLista = new Lista();
            $obLista->setTitulo('Funcionalidade '.$stNomeFuncionalidade );
            $obLista->setRecordSet($rsAcao);
            $obLista->setMostraPaginacao(false);
            $obLista->addCabecalho('&nbsp;', 5 );
            $obLista->addCabecalho('Código', 10 );
            $obLista->addCabecalho('Ação', 85 );
            $obLista->addCabecalho('&nbsp;', 0 );

            $obLista->addDado();
            $obLista->ultimoDado->setAlinhamento('ESQUERDA');
            $obLista->ultimoDado->setCampo('cod_acao');
            $obLista->commitDado();

            $obLista->addDado();
            $obLista->ultimoDado->setAlinhamento('ESQUERDA');
            $obLista->ultimoDado->setCampo('nom_acao');
            $obLista->commitDado();

            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao('selecionar');
            $obLista->ultimaAcao->setFuncao( true );
            $obLista->ultimaAcao->setLink( "JavaScript:retornaAcao()");
            $obLista->ultimaAcao->addCampo("4","cod_acao");
            $obLista->ultimaAcao->addCampo("5","nom_acao");
            $obLista->commitAcao();
            $obFormularioModulo->addLista($obLista);
            unset($rsAcao);
        }
    }
    $obFormularioGestao->addFormularioAbas($obFormularioModulo);
}
$obFormularioGestao->defineBarra(array($obBtnFechar),'','');
$obFormularioGestao->show();
?>

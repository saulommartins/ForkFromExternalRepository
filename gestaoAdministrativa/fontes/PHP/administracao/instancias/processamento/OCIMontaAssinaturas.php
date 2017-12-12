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
    * Oculto de Assinaturas Configuráveis
    * Data de Criação: 06/11/2007

    * @author Analista: Anderson Konze
    * @author Desenvolvedor: Leopoldo Braga Barreiro

    $Id: OCIMontaAssinaturas.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAssinatura.class.php"  );
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAssinaturaModulo.class.php"  );

$stJs = "";
switch ($_REQUEST['stCtrlAs']) {
    // Escolhe Assinatura
    case 'selecionaAssinatura':
        if ( isset($_REQUEST['assina']) && strpos($_REQUEST['assina'], '_') > 0 ) {
            $assinaturas = Sessao::read('assinaturas');
            $arAssinaturasSelecionadas = $assinaturas['selecionadas'];
            list ($prefixo, $id) = explode('_', $_REQUEST['assina']);
            --$id;

            $boInseriu = false;
            foreach ($arAssinaturasSelecionadas as $inChave => $arAssinatura) {
                if ($arAssinatura['inId'] == $id) {
                    if (isset($_REQUEST['papel'])) {
                        $arAssinaturasSelecionadas[$inChave]['papel'] = $_REQUEST['papel'];
                        $boInseriu = true;
                    }
                }
            }

            if (!$boInseriu) {
                $arAssinaturaDisponiveis = $assinaturas['disponiveis'];
                foreach ($arAssinaturaDisponiveis as $arAssinatura) {
                    if ($arAssinatura['inId'] == $id) {
                        if (isset($_REQUEST['papel'])) {
                            $arAssinatura['papel'] = $_REQUEST['papel'];
                        }
                        $arAssinaturasSelecionadas[] = $arAssinatura;
                        break;
                    }
                }
            }

            $assinaturas['selecionadas'] = $arAssinaturasSelecionadas;
            Sessao::write('assinaturas', $assinaturas);
        }
    break;
    // Retira Assinatura
    case 'retiraAssinatura':
        if ( isset($_REQUEST['assina']) && strpos($_REQUEST['assina'], '_') > 0 ) {
            list ($prefixo, $id) = explode('_', $_REQUEST['assina']);
            --$id;
            $arAssinaturasSelecionadas = array();
            $assinaturas = Sessao::read('assinaturas');
            foreach ($assinaturas['selecionadas'] as $arAssinatura) {
                if ($arAssinatura['inId'] != $id) {
                    $arAssinaturasSelecionadas[] = $arAssinatura;
                }
            }
            $assinaturas['selecionadas'] = $arAssinaturasSelecionadas;
            Sessao::write('assinaturas', $assinaturas);
        }
    break;
    // Limpa Todas as Assinaturas
    case 'limpaLista':
        $assinaturas = Sessao::read('assinaturas');
        $assinaturas['disponiveis'] = array();
        $assinaturas['selecionadas'] = array();
        Sessao::write('assinaturas', $assinaturas);
        $stJs .= "document.getElementById('spnListaAssinaturas').innerHTML = '';\n";
    break;
    // Monta Lista de Assinaturas (dos módulos selecionados)
    case 'montaLista':
        $rsAssinaturasModulo = new RecordSet;
        $rsAssinaturas = new RecordSet;
        $obTAssinaturaModulo = new TAdministracaoAssinaturaModulo;
        $obTAssinaturaModulo->setDado( 'exercicio', Sessao::getExercicio() );
        $obTAssinaturaModulo->setDado( 'cod_modulo',  Sessao::getModulo() );
        $stFiltro = "";
        if ( isset($_REQUEST['stCodEntidade']) && strlen($_REQUEST['stCodEntidade']) > 0 ) {
            if ( strpos( $_REQUEST['stCodEntidade'], ',') === false ) {
                $stFiltro .= " and assinatura_modulo.cod_entidade = '" . $_REQUEST['stCodEntidade'] . "' ";
            } else {
                $stFiltro .= " and assinatura_modulo.cod_entidade in ( " . $_REQUEST['stCodEntidade'] . " )  ";
            }
        }
        $obTAssinaturaModulo->recuperaNumCgmPorModulo( $rsAssinaturasModulo, $stFiltro );		// Recupera os numcgm do módulo atual

        if ( $rsAssinaturasModulo->getNumLinhas() > 0 ) {
            $arNumCgmModulo = array();
            while ( !$rsAssinaturasModulo->eof() ) {
                $arNumCgmModulo[] = $rsAssinaturasModulo->getCampo('numcgm');
                $rsAssinaturasModulo->proximo();
            }
            $obTAssinatura = new TAdministracaoAssinatura;
            $stCondicao = " and assinatura.numcgm in ( " .  implode(',', $arNumCgmModulo)  . " ) ";
            if ($_REQUEST['stCodEntidade'] != '') {
                $stCondicao.= " AND assinatura.cod_entidade IN ( " . $_REQUEST['stCodEntidade'] . " ) ";
            }
            $obTAssinatura->recuperaRelacionamento( $rsAssinaturas, $stCondicao );
        }

        if ($rsAssinaturas) {
            $inId = 0;
            $assinaturas = Sessao::read('assinaturas');
            $assinaturas['disponiveis'] = array();
            $assinaturas['selecionadas'] = array();
            Sessao::write('assinaturas', $assinaturas);

            while ( !$rsAssinaturas->eof() ) {
                $assinaturas['disponiveis'][$inId]['inId'] = $inId;
                $assinaturas['disponiveis'][$inId]['inCodEntidade'] = $rsAssinaturas->getCampo('cod_entidade');
                $assinaturas['disponiveis'][$inId]['inCGM'] = $rsAssinaturas->getCampo('numcgm');
                $assinaturas['disponiveis'][$inId]['stNomCGM'] = $rsAssinaturas->getCampo('nom_cgm');
                $assinaturas['disponiveis'][$inId]['stCargo'] = $rsAssinaturas->getCampo('cargo');
                $assinaturas['disponiveis'][$inId]['stCRC'] = $rsAssinaturas->getCampo('insc_crc');
                $assinaturas['disponiveis'][$inId]['timestamp'] = $rsAssinaturas->getCampo('timestamp');
                $rsAssinaturas->proximo();
                $inId++;
            }
            Sessao::write('assinaturas', $assinaturas);
            $assinaturas = Sessao::read('assinaturas');
            $rsAssinaturas->preenche( $assinaturas['disponiveis'] );

            $obLista = new Lista;
            $obLista->setTitulo( "Lista de Assinaturas" );
            $obLista->setMostraPaginacao( false );
            $obLista->setRecordSet( $rsAssinaturas );

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("&nbsp;");
            $obLista->ultimoCabecalho->setWidth( 2 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Entidade");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Nome");
            $obLista->ultimoCabecalho->setWidth( 40 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Cargo");
            $obLista->ultimoCabecalho->setWidth( 20 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("&nbsp;");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();

            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "inCodEntidade" );
            $obLista->ultimoDado->setAlinhamento( "CENTRO" );
            $obLista->commitDado();

            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "stNomCGM" );
            $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
            $obLista->commitDado();

            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "stCargo" );
            $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
            $obLista->commitDado();

            $obCheckbox = new Checkbox;
            $obCheckbox->setName( 'assinatura_' );
            $obCheckbox->setId( 'assinatura_' );
            $obCheckbox->obEvento->setOnClick('getIMontaAssinaturas(this)');

            $obLista->addDadoComponente( $obCheckbox, true );
            $obLista->ultimoDado->setAlinhamento( "CENTRO" );
            $obLista->commitDadoComponente();

            if (array_key_exists('papeis', $assinaturas)) {
                if ( is_array($assinaturas['papeis']) && count($assinaturas['papeis']) > 0 ) {
                    $obLista->addCabecalho();
                    $obLista->ultimoCabecalho->addConteudo("Papel");
                    $obLista->ultimoCabecalho->setWidth( 20 );
                    $obLista->commitCabecalho();
                    $obSelect = new Select;
                    $obSelect->setId  ( "papel_" );
                    $obSelect->setName( "papel_" );
                    $obSelect->setCampoId ( "papel_" );
                    $obSelect->obEvento->setOnChange('getPapelDisponivel(this)');
                    foreach ($assinaturas['papeis'] as $chave=>$valor) {
                        $obSelect->addOption($chave, $valor);
                    }
                    $obLista->addDadoComponente( $obSelect, true );
                    $obLista->commitDadoComponente();
                }
            }
            $obLista->montaHTML();
            $stHtml = $obLista->getHTML();

            $stHtml = str_replace("\n","",$stHtml);
            $stHtml = str_replace("  ","",$stHtml);
            $stHtml = str_replace("'","\\'",$stHtml);
            $stJs .= "document.getElementById('spnListaAssinaturas').innerHTML = '".$stHtml."';\n";
        }
    break;
    case 'montaListaConfiguravel':
        $rsAssinaturasModulo = new RecordSet;
        $rsAssinaturas = new RecordSet;
        $obTAssinaturaModulo = new TAdministracaoAssinaturaModulo;
        $obTAssinaturaModulo->setDado( 'exercicio', Sessao::getExercicio() );
        $obTAssinaturaModulo->setDado( 'cod_modulo',  Sessao::getModulo() );
        $stFiltro = "";
        $arCGM = array();
        if ( isset($_REQUEST['stCodEntidade']) && strlen($_REQUEST['stCodEntidade']) > 0 ) {
            if ( strpos( $_REQUEST['stCodEntidade'], ',') === false ) {
                $stFiltro .= " and assinatura_modulo.cod_entidade = '" . $_REQUEST['stCodEntidade'] . "' ";
            } else {
                $stFiltro .= " and assinatura_modulo.cod_entidade in ( '" . $_REQUEST['stCodEntidade'] . "' )  ";
            }
        }
        $obTAssinaturaModulo->recuperaNumCgmPorModulo( $rsAssinaturasModulo, $stFiltro );		// Recupera os numcgm do módulo atual

        if ( $rsAssinaturasModulo->getNumLinhas() > 0 ) {
            $arNumCgmModulo = array();
            while ( !$rsAssinaturasModulo->eof() ) {
                $arNumCgmModulo[] = $rsAssinaturasModulo->getCampo('numcgm');
                $rsAssinaturasModulo->proximo();
            }
            $obTAssinatura = new TAdministracaoAssinatura;
            $stCondicao = " and assinatura.numcgm in ( " .  implode(',', $arNumCgmModulo)  . " ) ";
            if ($_REQUEST['stCodEntidade'] != '') {
                $stCondicao.= " AND assinatura.cod_entidade IN ( " . $_REQUEST['stCodEntidade'] . " ) ";
            }
            $obTAssinatura->recuperaRelacionamento( $rsAssinaturas, $stCondicao );
        }

        if ($rsAssinaturas) {
            $inId = 0;
            $assinaturas = Sessao::read('assinaturas');
            $assinaturas['disponiveis'] = array();
            $assinaturas['selecionadas'] = array();
            Sessao::write('assinaturas', $assinaturas);

            while ( !$rsAssinaturas->eof() ) {
                $assinaturas['disponiveis'][$inId]['inId'] = $inId;
                $assinaturas['disponiveis'][$inId]['inCodEntidade'] = $rsAssinaturas->getCampo('cod_entidade');
                $assinaturas['disponiveis'][$inId]['inCGM'] = $rsAssinaturas->getCampo('numcgm');
                $assinaturas['disponiveis'][$inId]['stNomCGM'] = $rsAssinaturas->getCampo('nom_cgm');
                $assinaturas['disponiveis'][$inId]['stCargo'] = $rsAssinaturas->getCampo('cargo');
                $assinaturas['disponiveis'][$inId]['stCRC'] = $rsAssinaturas->getCampo('insc_crc');
                $assinaturas['disponiveis'][$inId]['timestamp'] = $rsAssinaturas->getCampo('timestamp');
                $inId++;
                $arCGM[$inId] = $rsAssinaturas->getCampo('numcgm');
                $rsAssinaturas->proximo();
            }
            Sessao::write('assinaturas', $assinaturas);
            $assinaturas = Sessao::read('assinaturas');
            $rsAssinaturas->preenche( $assinaturas['disponiveis'] );

            $obLista = new Lista;
            $obLista->setTitulo( "Lista de Assinaturas" );
            $obLista->setMostraPaginacao( false );
            $obLista->setRecordSet( $rsAssinaturas );

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("&nbsp;");
            $obLista->ultimoCabecalho->setWidth( 2 );
            $obLista->commitCabecalho();
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Entidade");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Nome");
            $obLista->ultimoCabecalho->setWidth( 40 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Cargo");
            $obLista->ultimoCabecalho->setWidth( 20 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("&nbsp;");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();

            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "inCodEntidade" );
            $obLista->ultimoDado->setAlinhamento( "CENTRO" );
            $obLista->commitDado();

            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "stNomCGM" );
            $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
            $obLista->commitDado();

            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "stCargo" );
            $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
            $obLista->commitDado();

            $obCheckbox = new Checkbox;
            $obCheckbox->setName( 'assinatura_' );
            $obCheckbox->setId( 'assinatura_' );
            $obCheckbox->obEvento->setOnClick('getIMontaAssinaturas(this)');

            $obLista->addDadoComponente( $obCheckbox, true );
            $obLista->ultimoDado->setAlinhamento( "CENTRO" );
            $obLista->commitDadoComponente();

            if ( is_array($assinaturas['papeis']) && count($assinaturas['papeis']) > 0 && $_REQUEST['stModulo'] != 'nota_liquidacao') {
                $obLista->addCabecalho();
                $obLista->ultimoCabecalho->addConteudo("Papel");
                $obLista->ultimoCabecalho->setWidth( 20 );
                $obLista->commitCabecalho();
                $obSelect = new Select;
                $obSelect->setName( 'papel_' );
                $obSelect->setId  ( 'papel_' );
                $obSelect->setCampoId ( 'papel_' );
                $obSelect->obEvento->setOnChange('getPapelDisponivel(this)');
                foreach ($assinaturas['papeis'] as $chave=>$valor) {
                    $obSelect->addOption($chave, $valor);
                }
                $obLista->addDadoComponente( $obSelect, true );
                $obLista->commitDadoComponente();
            }
            $obLista->montaHTML();
            $stHtml = $obLista->getHTML();
            $stHtml = str_replace("\n","",$stHtml);
            $stHtml = str_replace("  ","",$stHtml);
            $stHtml = str_replace("'","\\'",$stHtml);
            $stJs .= "document.getElementById('spnListaAssinaturas').innerHTML = '".$stHtml."';\n";

            if ($_REQUEST['stModulo'] == "nota_empenho_autorizacao") {
                include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenhoAssinatura.class.php";

                $obTEmpenhoAssinatura = new TEmpenhoEmpenhoAssinatura;
                $obTEmpenhoAssinatura->setDado('exercicio', Sessao::getExercicio());
                $obTEmpenhoAssinatura->setDado('cod_entidade', $_REQUEST['stCodEntidade']);
                $obTEmpenhoAssinatura->setDado('cod_empenho', " ( select max(cod_empenho) from empenho.empenho_autorizacao WHERE cod_entidade =
'".$_REQUEST['stCodEntidade']."' AND exercicio = '".Sessao::getExercicio()."') " );
                $obTEmpenhoAssinatura->recuperaAssinaturasEmpenho($rsAssinaturasConfiguraveis);

            } elseif ($_REQUEST['stModulo'] == "autorizacao_empenho") {
                include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoAutorizacaoEmpenhoAssinatura.class.php"  );

                $obTEmpenhoAssinatura = new TEmpenhoAutorizacaoEmpenhoAssinatura;
                $obTEmpenhoAssinatura->setDado( 'exercicio', Sessao::getExercicio() );
                $obTEmpenhoAssinatura->setDado( 'cod_entidade', $_REQUEST['stCodEntidade'] );
                $obTEmpenhoAssinatura->setDado( 'cod_autorizacao', " (select max(cod_autorizacao) from empenho.autorizacao_empenho where exercicio = '".Sessao::getExercicio()."' and cod_entidade = '".$_REQUEST['stCodEntidade']."' ) " );
                $obTEmpenhoAssinatura->recuperaAssinaturasAutorizacao($rsAssinaturasConfiguraveis);

            } elseif ($_REQUEST['stModulo'] == "nota_empenho") {
                include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenhoAssinatura.class.php"  );

                $obTEmpenhoAssinatura = new TEmpenhoEmpenhoAssinatura;
                $obTEmpenhoAssinatura->setDado( 'exercicio', Sessao::getExercicio() );
                $obTEmpenhoAssinatura->setDado( 'cod_entidade', $_REQUEST['stCodEntidade'] );
                $obTEmpenhoAssinatura->setDado( 'cod_empenho', " (select max(cod_empenho) from empenho.empenho where exercicio = '".Sessao::getExercicio()."' and cod_entidade = ".$_REQUEST['stCodEntidade']." and cod_empenho <> (select COALESCE(max(cod_empenho), 0) from empenho.empenho_autorizacao where cod_entidade = ".$_REQUEST['stCodEntidade']." and exercicio = '".Sessao::getExercicio()."')) " );
                $obTEmpenhoAssinatura->recuperaAssinaturasEmpenho($rsAssinaturasConfiguraveis);

            } elseif ($_REQUEST['stModulo'] == "ordem_pagamento") {
                include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamentoAssinatura.class.php"  );

                $obTOPAssinatura = new TEmpenhoOrdemPagamentoAssinatura;
                $obTOPAssinatura->setDado( 'exercicio', Sessao::getExercicio() );
                $obTOPAssinatura->setDado( 'cod_entidade', $_REQUEST['stCodEntidade'] );
                $obTOPAssinatura->setDado( 'cod_ordem', " (select max(cod_ordem) from empenho.ordem_pagamento WHERE cod_entidade = ".$_REQUEST['stCodEntidade']." AND exercicio = '".Sessao::getExercicio()."') " );
                $obTOPAssinatura->recuperaAssinaturasOrdem($rsAssinaturasConfiguraveis);

            } elseif ($_REQUEST['stModulo'] == "nota_liquidacao") {
                include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoNotaLiquidacaoAssinatura.class.php"  );

                $obTLiquidacaoAssinatura = new TEmpenhoNotaLiquidacaoAssinatura;
                $obTLiquidacaoAssinatura->setDado( 'exercicio', Sessao::getExercicio() );
                $obTLiquidacaoAssinatura->setDado( 'cod_entidade', $_REQUEST['stCodEntidade'] );
                $obTLiquidacaoAssinatura->setDado( 'cod_nota', " (select max(cod_nota) from empenho.nota_liquidacao WHERE cod_entidade = ".$_REQUEST['stCodEntidade']." AND exercicio = '".Sessao::getExercicio()."') " );
                $obTLiquidacaoAssinatura->recuperaAssinaturasNotaLiquidacao($rsAssinaturasConfiguraveis);
            }

            $inCount = 0;
            $arAssinaturasConfiguraveis = array();
            $arAssinaturasSelecionadas = $assinaturas['selecionadas'];
            for ($i = 1; $i <= count($arCGM); $i++) {
                while ( !$rsAssinaturasConfiguraveis->eof() ) {
                    if ($arCGM[$i] == $rsAssinaturasConfiguraveis->getCampo('numcgm')) {
                        $stJs .= " $('assinatura_".$i."').checked = true; ";
                        if ($_REQUEST['stModulo'] != "nota_liquidacao") {
                            $stJs .= " document.frm.papel_".$i.".options[".$rsAssinaturasConfiguraveis->getCampo('num_assinatura')."].selected = true;";
                            $arAssinaturasConfiguraveis['papel'] = $rsAssinaturasConfiguraveis->getCampo('num_assinatura');
                        }
                        $arAssinaturasConfiguraveis['inId'] = $inCount;
                        $arAssinaturasConfiguraveis['inCodEntidade'] = $rsAssinaturasConfiguraveis->getCampo('cod_entidade');
                        $arAssinaturasConfiguraveis['inCGM'] = $rsAssinaturasConfiguraveis->getCampo('numcgm');
                        $arAssinaturasConfiguraveis['stNomCGM'] = $rsAssinaturasConfiguraveis->getCampo('nom_cgm');
                        $arAssinaturasConfiguraveis['stCargo'] = $rsAssinaturasConfiguraveis->getCampo('cargo');
                        $arAssinaturasConfiguraveis['stCRC'] = "";
                        $arAssinaturasSelecionadas[] = $arAssinaturasConfiguraveis;
                    }
                    $rsAssinaturasConfiguraveis->proximo();
                }
                $rsAssinaturasConfiguraveis->setPrimeiroElemento();
                $inCount++;
            }

            $assinaturas['selecionadas'] = $arAssinaturasSelecionadas;
            Sessao::write('assinaturas', $assinaturas);
    }
    break;

}

$assinaturas = Sessao::read('assinaturas');

if ( isset($assinaturas['existentes']) && count($assinaturas['existentes']) > 0 && $_REQUEST['stModulo'] != 'nota_liquidacao') {
    // Carrega as chaves dos papéis disponíveis para o tipo de documento
    $arPapeis = array_keys($assinaturas['papeis']);
    $inAuxInd = 0;
    foreach ($assinaturas['disponiveis'] as $arAssinaDisp) {
        foreach ($assinaturas['existentes'] as $arAssinaExist) {
            if ( ($arAssinaExist['numcgm'] == $arAssinaDisp['inCGM']) && ($arAssinaExist['cod_entidade'] == $arAssinaDisp['inCodEntidade']) ) {
                $arSelecionada = $arAssinaDisp;
                $arSelecionada['papel'] = $arPapeis[$arAssinaExist['num_assinatura']];
                $assinaturas['selecionadas'][] = $arSelecionada;
                $stIdCampo = 'assinatura_' . ($arAssinaDisp['inId'] + 1);
                $stIdSelect = 'papel_' . ($arAssinaDisp['inId'] + 1);
                $stJs .= " var objAux = document.getElementById('" . $stIdCampo . "'); ";
                $stJs .= " objAux.checked = true; ";
                $stJs .= " try {objAux.form.".$stIdSelect.".value = '".$arSelecionada['papel']."';} catch (e) {alert(e.message);} ";
                $inAuxInd++;
                unset($arSelecionada);
            }
        }
    }
    // Limpa $sessao->assinaturas['existentes']
    $assinaturas['existentes'] = array();
    Sessao::write('assinaturas', $assinaturas);
}

if ($stJs) {
    echo $stJs;
}

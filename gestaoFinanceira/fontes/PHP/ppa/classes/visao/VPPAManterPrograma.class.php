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
    * Classe de visao do Manter programas

    * Data de Criação   : 22/09/2008

    * @author Analista      : Bruno Ferreira
    * @author Desenvolvedor : Jânio Eduardo
    * @ignore

    * $Id:

    *Casos de uso: uc-02.09.02
*/
include_once 'VPPAUtils.class.php';
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoUsuario.class.php" );

class VPPAManterPrograma
{
    private $controller;
    private $utils;

    public function __construct($controller)
    {
        $this->controller = $controller;
        $this->utils = new VPPAUtils;
    }

    public function montaData($param)
    {
        if ($param['boNatureza']=='f' || (isset($param['boModificarNatureza']) && $param['boModificarNatureza'] == 'true' && $param['boNatureza']=='f')) {
            $obDtPeriodo = new Periodo;
            $obDtPeriodo->setName ( "dtPeriodo" );
            $obDtPeriodo->setRotulo ( "Data Período" );
            $obDtPeriodo->setNull ( false );
            $obDtPeriodo->obDataFinal->obEvento->setOnChange( "verificaPeriodoFinal();" );
            $obDtPeriodo->obDataInicial->obEvento->setOnChange( "verificaPeriodoInicial();" );
            $obDtPeriodo->obDataInicial->setValue($_REQUEST['inDtInicio']);
            $obDtPeriodo->obDataInicial->setId('stDataInicial');
            $obDtPeriodo->obDataFinal->setId('stDataFinal');
            $obDtPeriodo->obDataFinal->setValue($_REQUEST['inDtTermino']);
            $obDtPeriodo->obDataInicial->setNull(false);
            $obDtPeriodo->obDataFinal->setNull(false );

            //$obTxtValorGlobal = new Numerico;
            //$obTxtValorGlobal->setName     ("flValorGlobal"           );
            //$obTxtValorGlobal->setId       ("flValorGlobal"           );
            //$obTxtValorGlobal->setRotulo   ("Valor Global"            );
            //$obTxtValorGlobal->setTitle    ("Informe o valor global." );
            //$obTxtValorGlobal->setValue    ($_REQUEST["flValorGlobal"]);
            //$obTxtValorGlobal->setSize     (14                        );
            //$obTxtValorGlobal->setMaxLength(14                        );
            //$obTxtValorGlobal->setNull     (false                     );
            //$obTxtValorGlobal->setMinValue (0.01                      );
            //$obTxtValorGlobal->setNegativo (false                     );

            $obFormulario = new Formulario;
            $obFormulario->addComponente($obDtPeriodo);
            //$obFormulario->addComponente($obTxtValorGlobal);
            $obFormulario->montaInnerHTML();
            $stJs = "d.getElementById('spnDtPrograma').innerHTML = '". $obFormulario->getHTML(). "';\n";

        } elseif (isset($param['boModificarNatureza']) && $param['boModificarNatureza'] == 'false') {
            $stJs  = "f.boNatureza[1].checked = true;";
            $stJs .= "alertaAviso('O campo Natureza Temporal não pode ser alterado pois possui uma ação do tipo Projeto vinculada a este programa.', 'form', 'erro', '".Sessao::getId()."');";
        } else {
            $stJs = "d.getElementById('spnDtPrograma').innerHTML = '';\n";
        }

        echo $stJs;
    }

    public function montaUnidade($arParam)
    {
        $stJs  = "limpaSelect( f.inCodUnidade,0 );";
        if ($arParam['inCodOrgaoTxt'] != $arParam['hdnCodOrgao'] OR !isset($arParam['hdnCodOrgao'])) {
            $stJs .= "f.inCodUnidadeTxt.value = '';";
            if ($arParam['stAcao'] == 'alterar') {
                $stJs .= "f.hdnCodUnidade.value = '';";
                $stJs .= "f.hdnCodOrgao.value = '';";
            }
        } else {
            $stSelecionado = $arParam['hdnCodUnidade'];
        }
        $stJs .= "f.inCodUnidade.options[0] = new Option('Selecione',  '');";
        if ($arParam['inCodOrgaoTxt'] != "") {
            include_once CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php";

            $obROrcamentoDespesa = new ROrcamentoDespesa;
            $obROrcamentoDespesa->setExercicio(Sessao::getExercicio() );
            $obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $_REQUEST['inCodOrgaoTxt'] );
            $obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->setExercicio( Sessao::getExercicio() );
            $obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->consultar( $rsUnidade );

            $inCount = 0;
            while (!$rsUnidade->eof()) {
                $inCount++;
                $inId   = $rsUnidade->getCampo("num_unidade");
                $stDesc = $rsUnidade->getCampo("nom_unidade");
                if ($stSelecionado == $inId) {
                    $stSelected = 'selected';
            $inNumUnidadeSelec = $inId;
        } else {
                    $stSelected = '';
        }
                $stJs .= "f.inCodUnidade.options[$inCount] = new Option('".$stDesc."','".$inId."'); \n";
                $rsUnidade->proximo();
            }

        $stJs .= "f.inCodUnidade.value = '".$inNumUnidadeSelec."';\n";
        }

        echo $stJs;
    }

    public function sugereCodPrograma($arParam)
    {
        if (!$arParam['inCodProgramaSetorial']) {
            $stJs = "f.inCodPrograma.value='';";
            $stJs.= "f.inCodProgramaSetorialTxt.focus();";
            $stJs.= "alertaAviso('Selecione um Programa Setorial!', 'form', 'erro', '".Sessao::getId()."');";
        } else {
            $stSugerido = $this->buscaCodPrograma($arParam);

            $stJs = "f.inCodPrograma.value='$stSugerido';";
            $stJs.= "f.inCodPrograma.focus();";
        }

        return $stJs;
    }

    public function validaCodPrograma($arParam)
    {
        $stFiltro = "ppa.cod_ppa ='" .$arParam['inCodPPA']."' AND ";
        $stFiltro.= "programa.num_programa ='" .(int) $arParam['inCodPrograma']."' AND ";
        $stFiltro.= "ativo = 't'";

        $rsPrograma = $this->controller->recuperaPrograma($stFiltro, 'recuperaPrograma');

        if ($rsPrograma->getNumLinhas() > 0) {
            $stAviso = 'Número do programa já cadastrado para este PPA!';
        }

        if ($stAviso) {
            $stJs.= "f.inCodPrograma.focus();";
            $stJs.= "jq('#inCodPrograma').val('');";
            $stJs.= "alertaAviso('".$stAviso."', 'form', 'erro', '".Sessao::getId()."');";
        }

        return $stJs;
    }

    public function recuperaPPA(array $arParam)
    {
        $inCodPPA = $arParam['inCodPPA'] ? $arParam['inCodPPA'] : $arParam['inCodPPATxt'];
        $stJS = '';

        if ($inCodPPA) {
            $obRPPA = new RPPAManterPPA();
            $rsPPA = $obRPPA->pesquisa('TPPA', 'recuperaTodos', " cod_ppa = $inCodPPA ");

            if (!$rsPPA->eof()) {
                $inAnoInicioPPA = $rsPPA->getCampo('ano_inicio');
                $inAnoFinalPPA  = $rsPPA->getCampo('ano_final');
                $stJS .= 'document.frm.inAnoInicioPPA.value = '. $inAnoInicioPPA . ';';
                $stJS .= 'document.frm.inAnoFinalPPA.value = '. $inAnoFinalPPA . ';';
            }
        }

        return $stJS;
    }

    public function buscaMacroObjetivos(array $arParam)
    {
        //limpa o combo de macro objetivos
        $stJs .= "jq('#inCodMacroObjetivo').removeOption(/./);";
        $stJs .= "jq('#inCodMacroObjetivoTxt').val('');";
        $stJs .= "var arOption = { '' : 'Selecione', ";

        $inCodPPA = $arParam['inCodPPA'] ? $arParam['inCodPPA'] : $arParam['inCodPPATxt'];
        //se nao for vazio, busca os dados
        if (($inCodPPA != '')) {
            include_once CAM_GF_PPA_MAPEAMENTO . 'TPPAMacroObjetivo.class.php';

            $obTPPAMacroObjetivo = new TPPAMacroObjetivo;
            //Filtro para a consulta
            $stFiltro = ' WHERE cod_ppa = ' . $inCodPPA . ' ';
            $stOrder = ' ORDER BY cod_macro ';
            $obTPPAMacroObjetivo->recuperaTodos($rsMacroObjetivo, $stFiltro, $stOrder);

            //percorre todo o recordset montando o combo de macro objetivos
            while (!$rsMacroObjetivo->eof()) {
                $inCodMacro = $rsMacroObjetivo->getCampo('cod_macro');
                $stMacroObjetivo = str_replace(array("\n", "\r"), ' ', $rsMacroObjetivo->getCampo('descricao'));
                $stJs .= " '".$inCodMacro."' : '".$stMacroObjetivo."', ";

                $rsMacroObjetivo->proximo();
            }
        }

        $stJs .= '};';
        $stJs .= "jq('#inCodMacroObjetivo').addOption(arOption,true);";

        return $stJs;

    }

    public function buscaProgramasSetoriais(array $arParam)
    {
        //limpa o combo de macro objetivos
        $stJs .= "jq('#inCodProgramaSetorial').removeOption(/./);";
        $stJs .= "jq('#inCodProgramaSetorialTxt').val('');";
        $stJs .= "var arOption = { '' : 'Selecione', ";

        $inCodMacro = $arParam['inCodMacroObjetivo'] ? $arParam['inCodMacroObjetivo'] : $arParam['inCodMacroObjetivo'];
        //se nao for vazio, busca os dados
        if (($inCodMacro != '')) {
            include_once CAM_GF_PPA_MAPEAMENTO . 'TPPAProgramaSetorial.class.php';

            $obTPPAProgramaSetorial = new TPPAProgramaSetorial;
            //Filtro para a consulta
            $stFiltro = ' WHERE cod_macro = ' . $inCodMacro . ' ';
            $obTPPAProgramaSetorial->recuperaTodos($rsProgramaSetorial, $stFiltro);

            //percorre todo o recordset montando o combo de programa setorial
            while (!$rsProgramaSetorial->eof()) {
                $inCodSetorial = $rsProgramaSetorial->getCampo('cod_setorial');
                $stDescricaoSetorial = str_replace(array("\n", "\r"), ' ', $rsProgramaSetorial->getCampo('descricao'));
                $stJs .= " '".$inCodSetorial."' : '".$stDescricaoSetorial."', ";

                $rsProgramaSetorial->proximo();
            }
        }

        $stJs .= '};';
        $stJs .= "jq('#inCodProgramaSetorial').addOption(arOption,true);";

        return $stJs;

    }

    public function GeraHidden($nome,$value)
    {
        $obHdn =  new Hidden;
        $obHdn->setName ( "{$nome}[]");
        $obHdn->setValue( $value );
        $obHdn->montaHtml();

        return $obHdn->getHtml();
    }

    public function montaLista($arValores, $stAcao = '', $opt, $emHtml = false, $boAcao = true)
    {
        if (is_array($arValores)) {
            $rsRecordSet = new RecordSet;
            $rsRecordSet->preenche( $arValores );
            $obLista = new Lista;
            $obLista->setMostraPaginacao( false );
            $obLista->setTitulo( $opt["cabecalho"]);

            $obLista->setRecordSet( $rsRecordSet );

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
            $obLista->ultimoCabecalho->setWidth   ( 5        );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo($opt['campoCabecalho'] );
            $obLista->ultimoCabecalho->setWidth   ( 80          );
            $obLista->commitCabecalho();

            if ($boAcao) {
                $obLista->addCabecalho();
                $obLista->ultimoCabecalho->addConteudo( "Ação" );
                $obLista->ultimoCabecalho->setWidth   ( 10     );
                $obLista->commitCabecalho();
            }
            ////dados

            $obLista->addDado();
            $obLista->ultimoDado->setAlinhamento( "ESQUERDA"   );
            $obLista->ultimoDado->setCampo      ( $opt['campo'] );
            $obLista->commitDado();

            if ($boAcao) {
                $obLista->addAcao();
                $obLista->ultimaAcao->setAcao  ( "EXCLUIR"                                                 );
                $obLista->ultimaAcao->setFuncao( true                                                      );
                $obLista->ultimaAcao->setLink  ( "javascript:".$opt['excluir']."();" );
                $obLista->ultimaAcao->addCampo ( "",$opt['codigo']);
                $obLista->commitAcao();
            }
            $obLista->montaHTML();

            $sthtml = $obLista->getHTML();

                if ($emHtml) {
                    $stJs  = $sthtml;
                } else {
                    $sthtml = str_replace                  ( "\n","",$sthtml        );
                    $sthtml = str_replace                  ( "  ","",$sthtml        );
                    $sthtml = str_replace                  ( "'","\\'",$sthtml      );

                    $stJs.= " d.getElementById('{$opt['span']}').innerHTML  = '".$sthtml."'; \n";
                }
        } else $stJs.= " d.getElementById('{$opt['span']}').innerHTML  = ''; \n";

        return $stJs;
    }

    public function buscaListaIndicador($arParam, $boConsulta = false)
    {
        # Define cabeçalhos da lista.
        $arCabecalhos = array(
            array('cabecalho' => 'Indicador'             , 'width' => 5),
            array('cabecalho' => 'Unidade'               , 'width' => 5),
            array('cabecalho' => 'Índice Recente'        , 'width' => 5),
            array('cabecalho' => 'Data do Índice Recente', 'width' => 5),
            array('cabecalho' => 'Índice Desejado'       , 'width' => 5),
            array('cabecalho' => 'Fonte do Índice'       , 'width' => 5),
            array('cabecalho' => 'Periodicidade'         , 'width' => 5),
            array('cabecalho' => 'Base Geográfica'       , 'width' => 5),
            array('cabecalho' => 'Fonte de Cálculo'      , 'width' => 5),
         );

        include_once CAM_GF_PPA_MAPEAMENTO."TPPAPeriodicidade.class.php";
        $TPPAPeriodicidade = new TPPAPeriodicidade;
        $TPPAPeriodicidade->recuperaPeriodicidade($rsPeriodicidade);

        include_once CAM_GA_ADM_NEGOCIO."RUnidadeMedida.class.php";
        $obRUnidadeMedida = new RUnidadeMedida;
        $obRUnidadeMedida->listar($rsUnidadeMedida, " cod_unidade ");

        # Define os campos da lista.
        if ($boConsulta) {

            $arComponentes = array(
            array('tipo'  => 'label', 'name'  => 'arDescIndicador',    'campo' => 'stDescIndicador'),
            array('tipo'  => 'label', 'name'  => 'arUnidadeMedida',    'campo' => 'stUnidadeMedida'),
            array('tipo'  => 'label', 'name'  => 'arIndiceRecente',    'campo' => 'flIndiceRecente'),
            array('tipo'  => 'label', 'name'  => 'arDtIndiceRecente',  'campo' => 'dtIndiceRecente'),
            array('tipo'  => 'label', 'name'  => 'arIndiceDesejado',   'campo' => 'flIndiceDesejado'),
            array('tipo'  => 'label', 'name'  => 'arFonteIndice',      'campo' => 'stFonteIndice'),
            array('tipo'  => 'label', 'name'  => 'arPeriodicidade',    'campo' => 'stPeriodicidade'),
            array('tipo'  => 'label', 'name'  => 'arBaseGeografica',   'campo' => 'stBaseGeografica'),
            array('tipo'  => 'label', 'name'  => 'arFormaCalculo',     'campo' => 'stFormaCalculo'),
           );

        } else {
        $arComponentes = array(
            array('tipo'  => 'TextBox',
                  'name'  => 'arDescIndicador',
                  'campo' => 'stDescIndicador',
                  'null'  => false,
                  'size'  => 10,
                  'setMaxLength' => 100),
            array('tipo'  => 'Select',
                  'name'  => 'arUnidadeMedida',
                  'campo' => 'stUnidadeMedida',
                  'preenche' => $rsUnidadeMedida,
                  'campoId' => '[cod_unidade]-[cod_grandeza]',
                  'campoDesc' => 'nom_unidade',
                  'selecione' => true,
                  'null'  => false),
            array('tipo'  => 'Numerico',
                  'name'  => 'arIndiceRecente',
                  'campo' => 'flIndiceRecente',
                  'decimais' => '2',
                  'onChange' => 'testarIndiceRecente(this);',
                  'null'  => false),
            array('tipo'  => 'Data',
                  'name'  => 'arDtIndiceRecente',
                  'campo' => 'dtIndiceRecente',
                  'null'  => false),
            array('tipo'  => 'Numerico',
                  'name'  => 'arIndiceDesejado',
                  'campo' => 'flIndiceDesejado',
                  'decimais' => '2',
                  'onChange' => 'testarIndiceDesejado(this);',
                  'null' => false),
            array('tipo'  => 'TextBox',
                  'name'  => 'arFonteIndice',
                  'campo' => 'stFonteIndice',
                  'null'  => false,
                  'size'  => 10,
                  'setMaxLength' => 100),
            array('tipo'  => 'Select',
                  'name'  => 'arPeriodicidade',
                  'campo' => 'stPeriodicidade',
                  'preenche' => $rsPeriodicidade,
                  'campoId' => 'cod_periodicidade',
                  'campoDesc' => 'nom_periodicidade',
                  'selecione' => true,
                  'style' => 'width: 100px',
                  'null'  => false),
            array('tipo'  => 'TextBox',
                  'name'  => 'arBaseGeografica',
                  'campo' => 'stBaseGeografica',
                  'null'  => false,
                  'size'  => 10,
                  'setMaxLength' => 100),
            array('tipo'  => 'TextBox',
                  'name'  => 'arFormaCalculo',
                  'campo' => 'stFormaCalculo',
                  'null'  => false,
                  'size'  => 10,
                  'setMaxLength' => 100),

           );
        }
        $arValores = array();

        # Novos valores acrescentados no array.
        if ($_REQUEST['stDescIndicador']) {
        $arValores[] =
            array('stDescIndicador'  => $_REQUEST['stDescIndicador'],
                  'stUnidadeMedida'  => $_REQUEST['stUnidadeMedida'],
                  'flIndiceRecente'  => $_REQUEST['flIndiceRecente'],
                  'dtIndiceRecente'  => $_REQUEST['dtIndiceRecente'],
                  'flIndiceDesejado' => $_REQUEST['flIndiceDesejado'],
                  'stFonteIndice'    => $_REQUEST['stFonteIndice'],
                  'stPeriodicidade'  => $_REQUEST['stPeriodicidade'],
                  'stBaseGeografica' => $_REQUEST['stBaseGeografica'],
                  'stFormaCalculo'   => $_REQUEST['stFormaCalculo'],
                  );
        } else {
            for ($x=0;$x<count($arParam);$x++) {

                $arValores[$x] =
                array('stDescIndicador'  => $arParam[$x]['descricao'],
                      'stUnidadeMedida'  => $arParam[$x]['cod_unidade']."-".$arParam[$x]['cod_grandeza'],
                      'flIndiceRecente'  => $arParam[$x]['indice_recente'],
                      'dtIndiceRecente'  => $arParam[$x]['dt_indice_recente'],
                      'flIndiceDesejado' => $arParam[$x]['indice_desejado'],
                      'stFonteIndice'    => $arParam[$x]['fonte'],
                      'stPeriodicidade'  => $arParam[$x]['cod_periodicidade'],
                      'stBaseGeografica' => $arParam[$x]['base_geografica'],
                      'stFormaCalculo'   => $arParam[$x]['forma_calculo'],
                 );
            }
        }

        # Recupera os valores anteriores em array.
        if (count($arParam['arDescIndicador'])) {
            foreach ($arParam['arDescIndicador'] as $inChave => $inValor) {
                $arLinha = array();

                foreach ($arComponentes as $arCampos) {
                    $arLinha[$arCampos['campo']] = stripslashes($arParam[$arCampos['name']][$inChave]);
                }

                array_unshift($arValores, $arLinha);
            }
        }

        $stHTML = $this->utils->montaLista('Indicadores', 'Lista de Indicadores', $arCabecalhos, $arComponentes, $arValores, $boConsulta);

        $stJs = '$(\'spnListaIndice\').innerHTML = \'' . $stHTML . '\';';
        $stJs .= "document.getElementById('stDescIndicador').value = '';";
        $stJs .= "document.getElementById('stUnidadeMedida').value = '';";
        $stJs .= "document.getElementById('flIndiceRecente').value = '';";
        $stJs .= "document.getElementById('dtIndiceRecente').value = '';";
        $stJs .= "document.getElementById('stFonteIndice').value = '';";
        $stJs .= "document.getElementById('stPeriodicidade').value = '';";
        $stJs .= "document.getElementById('stBaseGeografica').value = '';";
        $stJs .= "document.getElementById('stFormaCalculo').value = '';";
        $stJs .= "document.getElementById('stDescIndicador').focus();";

        if ($arParam['flIndiceDesejado']) {
            $stJs .= "document.getElementById('flIndiceDesejado').value = ''";
        }

        return $stJs;
    }

    //inclusão de programas
    public function incluir($param)
    {
            return $this->controller->incluirPrograma($param);
    }

    public function getFiltro($param)
    {
        if ($param['inCodPrograma'] != '') {
            $stFiltro[] .= "programa.cod_programa = '".$param['inCodPrograma']."'";
        }
        if ($param['inCodProgramaSetorial']) {
            $stFiltro [] .= "programa_setorial.cod_setorial = " . $param['inCodProgramaSetorial'] . " ";
        }
        if ($param['inCodPPA']) {
            $stFiltro[] .= "ppa.cod_ppa = '".$param['inCodPPA']."'";
        }
        if ($param['inNumPrograma'] != "") {
            $stFiltro[] .= "programa.num_programa = '".intval($param['inNumPrograma'])."'";
        }
        if ($param['boNatureza']) {
            $stFiltro[] .= "programa_dados.continuo = '" .$param['boNatureza']."'";
        }
        if ($param['inIdPrograma']) {

            $stLike[] = explode(' ', $param['inIdPrograma']);

            for ($x = 0; $x < count($stLike); $x++) {
                $arLike .= "'%".$stLike[0][$x]."%'";
            }

            $stFiltro[] .= 'programa_dados.identificacao like '.$arLike;
        }
        if ($param['inCodOrgao']) {
            $stFiltro[] .= "programa_dados.num_orgao = '".$param['inCodOrgao']."'";;
        }
        if ($param['inCodUnidade']) {
            $stFiltro[] .= "programa_dados.num_unidade = '".$param['inCodUnidade']."'";;
        }

        if ($stFiltro) {
            foreach ($stFiltro as $chave => $valor) {
                if ($chave == 0) {
                    $return .= $valor;
                } else {
                    $return .= " AND ".$valor;
                }
            }
        }

        return $return;
    }

    public function getFiltroLista($param)
    {
        if ($param['inCodPrograma'] != "") {

            $stFiltro[] .= "cod_programa = '".str_pad($param['inCodPrograma'], 4, 0, STR_PAD_LEFT)."'";
        }
        if ($param['inCodPPA']) {
            $stFiltro[] .= "cod_ppa = '".$param['inCodPPA']."'";
        }
        if ($param['inNumPrograma'] != "") {
            $stFiltro[] .= "num_programa = '".str_pad($param['inNumPrograma'], 4, 0, STR_PAD_LEFT)."'";
        }
        if ($param['boNatureza'] && $param['boNatureza'] != 'n') {
            $stFiltro[] .= "bo_continuo = '" .$param['boNatureza']."'";
        }
        if ($param['inIdPrograma']) {

            $stLike[] = explode(' ', $param['inIdPrograma']);

            for ($x = 0; $x < count($stLike); $x++) {
                $arLike .= "'%".$stLike[0][$x]."%'";
            }

            $stFiltro[] .= 'identificacao like '.$arLike;
        }
        if ($param['inCodOrgao']) {
            $stFiltro[] .= "num_orgao = '".$param['inCodOrgao']."'";;
        }
        if ($param['inCodUnidade']) {
            $stFiltro[] .= "num_unidade = '".$param['inCodUnidade']."'";;
        }

        if ($stFiltro) {
            foreach ($stFiltro as $chave => $valor) {
                if ($chave == 0) {
                    $return .= $valor;
                } else {
                    $return .= " AND ".$valor;
                }
            }
        }

        return $return;
    }

    public function buscaPrograma($arParametros, $liberarFrames=true)
    {
    $stFiltro  = $this->getFiltro($arParametros);
        $stFiltro .= " AND programa.ativo = 't'";

        if ($arParametros['stAcao'] == 'excluir') {
            $stFiltro .= " AND NOT EXISTS ( SELECT 1
                                              FROM ppa.acao
                                             WHERE programa.cod_programa = acao.cod_programa
                                          ) ";
        }

    if ($liberarFrames) {
        SistemaLegado::LiberaFrames(true, false);
    }

        return $this->controller->recuperaPrograma($stFiltro, 'recuperaPrograma');
    }

    public function buscaProgramaLista($arParametros)
    {
        $stFiltro = $this->getFiltroLista($arParametros);
        if ($stFiltro != '') {
            $stFiltro .= " AND ";
        }
        $stFiltro .= " ( ( ativo = 't' AND cod_ppa is not null ) OR (cod_ppa is null) ) ";

        if ($arParametros['stAcao'] == 'excluir') {
            $stFiltro .= " AND NOT EXISTS ( SELECT 1
                                              FROM ppa.acao
                                             WHERE cod_programa = acao.cod_programa
                                          ) ";
        }

        SistemaLegado::LiberaFrames(true,false);

        return $this->controller->recuperaProgramaLista($stFiltro, 'recuperaProgramaLista');
    }

    public function buscaProgramaListaExclusao($arParametros)
    {
        $stFiltro = $this->getFiltroLista($arParametros);
        if ($stFiltro != '') {
            $stFiltro .= " AND ";
        }
        $stFiltro .= " ( ( ativo = 't' AND cod_ppa is not null ) OR (cod_ppa is null) ) ";

        if ($arParametros['stAcao'] == 'excluir') {
            $stFiltro .= " AND NOT EXISTS ( SELECT 1
                                              FROM ppa.acao
                                             WHERE tabela.cod_programa = acao.cod_programa
                                          ) ";
        }

        SistemaLegado::LiberaFrames(true,false);

        return $this->controller->recuperaProgramaLista($stFiltro, 'recuperaProgramaListaExclusao');
    }

    public function buscaIndicadores($param,$boConsulta=false)
    {
        $stFiltro = $this->getFiltro($param);
        $obRSIndicador = $this->controller->recuperaPrograma($stFiltro, 'recuperaIndicador');
        $arIndicador['arIndicadores'] = $obRSIndicador->arElementos;
        $stJs = $this->buscaListaIndicador($arIndicador['arIndicadores'],$boConsulta);
        sistemaLegado::executaFrameOculto($stJs);
    }

    public function buscaCodPrograma($param)
    {
        $rsPrograma = $this->controller->getProximoCodPrograma($param);

        return $rsPrograma->getCampo('cod_programa');
    }

    public function alterar($arParam)
    {
        return $this->controller->alterar($arParam);
    }

    public function excluir($arParam)
    {
        if ($this->verificaHomologacao($arParam['inCodPPA'])) {
            $arParam['boHomologado'] = true;
        }

        $return = $this->controller->excluir($arParam);

        return $return;
    }

    public function verificaHomologacao($CodPPA)
    {
        $obResult = $this->controller->buscaPPAHomologado($CodPPA);

        if ($obResult->Eof()) {
            return false;
        } else {
            return true;
        }
    }

    public function exibirFuncionalidadePrograma($stCondicao = '')
    {
        return $this->controller->getFuncionalidadePrograma($stCondicao);
    }

    public function limpaListaIndice($arParam)
    {
        return "$('spnListaIndice').innerHTML = '&nbsp;';";
    }
}

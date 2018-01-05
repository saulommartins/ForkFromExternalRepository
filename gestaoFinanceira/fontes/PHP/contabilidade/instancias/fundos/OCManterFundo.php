<?php
	
	include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';

	ini_set("display_errors", 1);
	error_reporting(E_ALL);

	function recuperarOrgaosPorEntidade($exercicio, $value) {
		include_once CAM_GF_ORC_NEGOCIO . 'ROrcamentoOrgaoOrcamentario.class.php';

		$obROrgaoOrcamentario = new ROrcamentoOrgaoOrcamentario();
		$obROrgaoOrcamentario->setExercicio($exercicio);
		$obROrgaoOrcamentario->setCodigoEntidade($value);

		$rsOrgaosOrcamentarios = new RecordSet;
		$obROrgaoOrcamentario->listar($rsOrgaosOrcamentarios);

		$js = "limpaSelect(f.inCodOrgao,0); \n";
        $js .= "f.inCodOrgao.options[0] = new Option('Selecione','', 'selected');\n";
        $selected = 'selected="selected"';

        foreach ($rsOrgaosOrcamentarios->arElementos as $key => $orgaoOrcamentario) {
            $js .= "f.inCodOrgao.options[$key + 1] = new Option('".$orgaoOrcamentario['nom_orgao']."', '".$orgaoOrcamentario['num_orgao']."', '".$selected."'); \n";

            $selected = "";
        }

        $js .= "limpaSelect(f.inCodUnidade, 0); \n";
        $js .= "f.inCodUnidade.options[0] = new Option('Selecione um órgão orçamentário.','', 'selected');\n";

        echo $js;
	}

	function recuperarUnidadesPorOrgao($exercicio, $value) {
		include_once CAM_GF_ORC_NEGOCIO . 'ROrcamentoUnidadeOrcamentaria.class.php';

		$obRUnidadeOrcamentaria = new ROrcamentoUnidadeOrcamentaria();
		$obRUnidadeOrcamentaria->setExercicio($exercicio);
		$obRUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($value);

		$rsUnidadesOrcamentarias = new RecordSet;
		$obRUnidadeOrcamentaria->consultar($rsUnidadesOrcamentarias);

		$js = "limpaSelect(f.inCodUnidade,0); \n";
        $js .= "f.inCodUnidade.options[0] = new Option('Selecione','', 'selected');\n";
        
        foreach ($rsUnidadesOrcamentarias->arElementos as $key => $orgaoOrcamentario) {
            $js .= "f.inCodUnidade.options[$key + 1] = new Option('".$orgaoOrcamentario['nom_unidade']."', '".$orgaoOrcamentario['num_unidade']."', ''); \n";
        }

        echo $js;
	}

	function extinguirFundo($exercicio, $value)
	{
		include_once CAM_GF_CONT_NEGOCIO . "RContabilidadeFundo.class.php";
		$obRContabilidadeFundo = new RContabilidadeFundo();
		$obRContabilidadeFundo->extinguirFundo($exercicio, $value);	

		header("Location: LSExtinguirFundo.php");
	}

	if (function_exists($_GET['stCtrl'])) {
		call_user_func($_GET['stCtrl'], $_GET['exercicio'], $_GET['value']);
	}
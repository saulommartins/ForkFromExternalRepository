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
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/

include_once 'dataBaseLegado.class.php';
include_once (CAM_GP_PAT_MAPEAMENTO."TPatrimonioNatureza.class.php" );
include_once (CAM_GP_PAT_MAPEAMENTO."TPatrimonioGrupo.class.php" );
include_once (CAM_GP_PAT_MAPEAMENTO."TPatrimonioEspecie.class.php" );

$valor2 = $_REQUEST['valor2'];
$variavel = $_REQUEST['variavel'];
$nom_atributo = $_REQUEST['nom_atributo'];
$ordenacao = $_REQUEST['ordenacao'];
$codNatureza = $_REQUEST['codNatureza'];
$nomNatureza = $_REQUEST['nomNatureza'];
$codGrupo = $_REQUEST['codGrupo'];
$nomGrupo = $_REQUEST['nomGrupo'];
$codEspecie = $_REQUEST['codEspecie'];
$nomEspecie = $_REQUEST['nomEspecie'];

if (isset($valor2)) {
    $valor = $valor2;
}

switch ($variavel) {

    // caso tenha os tres valores, monta os tres combos
    case 'NatGrpEsp':

        if ($valor != "") {

        $arVariaveis     = preg_split( "/[^a-zA-Z0-9]/", $valor );
        $codNaturezaF    = $arVariaveis[0];
        $codGrupoF       = $arVariaveis[1];
        $codEspecieF     = $arVariaveis[2];

        //VERIFICA O COMBO ORGAO
        $js .=  "var iContNatureza = 0;\n";
        $js .=  "var iTamNatureza = f.codNatureza.options.length - 1;\n";
        $js .=  "while (iTamNatureza >= iContNatureza) {\n";
        $js .=  "    if ( f.codNatureza.options[iContNatureza].value == ".(integer) $codNaturezaF.") {\n";
        $js .=  "        f.codNatureza.options[iContNatureza].selected = true;\n";
        $js .=  "        break;\n";
        $js .=  "    }\n";
        $js .=  "    iContNatureza++;\n";
        $js .=  "}\n";
        $js .=  "if (iContNatureza > iTamNatureza) {\n";
        $js .=  "    limpaSelect(f.codGrupo,1); \n";
        $js .=  "    limpaSelect(f.codEspecie,1); \n";
        $js .=  "}\n";

        if ($anoExercicio != "") {
            $anoExercicio = " and ano_exercicio = ".$anoExercicio." ";
        }
        $sSQL = "SELECT
                    cod_natureza, nom_natureza
                FROM
                    patrimonio.natureza
                WHERE
                    cod_natureza = '".$codNaturezaF."'
                    ".$anoExercicio."
                ORDER
                    by nom_natureza";

        $dbNat = new dataBaseLegado;
        $dbNat->abreBD();
        $dbNat->abreSelecao($sSQL);
        $dbNat->vaiPrimeiro();

        if ( !$dbNat->eof() ) {

            $sSQL = "
                SELECT
                    cod_grupo, cod_natureza, nom_grupo
                FROM
                    patrimonio.grupo
                WHERE
                    cod_natureza = '".$codNaturezaF."'
                    ".$anoExercicio."
                ORDER
                    by nom_grupo ";

            $dbGrp = new dataBaseLegado;
            $dbGrp->abreBD();
            $dbGrp->abreSelecao($sSQL);
            $dbGrp->vaiPrimeiro();
            $boRegistroGrp = $dbGrp->eof();
            $contGrp = 1;

            if (!$boRegistroGrp) {
                $js .= "limpaSelect(f.codGrupo,0); \n";
                $js .= "f.codGrupo.options[0] = new Option('Selecione','xxx');\n";
                $js .= "limpaSelect(f.codEspecie,0); \n";
                $js .= "f.codEspecie.options[0] = new Option('Selecione','xxx');\n";

                //monta combo de Grupos
                while (!$dbGrp->eof()) {
                    $codGrupoC  = trim($dbGrp->pegaCampo("cod_grupo"));
                    $nomGrupoC  = trim($dbGrp->pegaCampo("nom_grupo"));

                    if ($codGrupoC == $codGrupoF) {
                        $selected = ", true";
                        $js .=  "f.nomGrupo.value = \"".$nomGrupoC."\";\n";

                    } else {
                        $selected = "";
                    }

                    $nomeGrupo = $codGrupoC." - ".$nomGrupoC;

                    $js .= "f.codGrupo.options[$contGrp] = new Option('".addslashes($nomeGrupo)."','".$codGrupoC."'".$selected."); \n";

                    $contGrp++;
                    $dbGrp->vaiProximo();
                }

                $dbGrp->limpaSelecao();
                $dbGrp->fechaBD();
                if ($codGrupoF) {
                    $sSQL = "
                        SELECT
                            E.*
                        FROM
                            patrimonio.especie  as E
                        WHERE
                                E.cod_natureza = '".$codNaturezaF."'
                            AND E.cod_grupo    = '".$codGrupoF."'
                            ".$anoExercicio."
                        ORDER
                            by E.nom_especie ";
                    $dbEsp = new dataBaseLegado;
                    $dbEsp->abreBD();
                    $dbEsp->abreSelecao($sSQL);
                    $dbEsp->vaiPrimeiro();
                    $boRegistroDep = $dbEsp->eof();
                    $contEsp = 1;

                    //monta combo de Especies
                    $js .= "limpaSelect(f.codEspecie,0); \n";
                    $js .= "f.codEspecie.options[0] = new Option('Selecione','xxx');\n";

                    while (!$dbEsp->eof()) {
                        $codEspecieC  = trim($dbEsp->pegaCampo("cod_especie"));
                        $nomEspecieC = str_replace('\'','\\\'',trim($dbEsp->pegaCampo("nom_especie")));
                        if ($codEspecieC == $codEspecieF) {
                            $selected = ", true";
                            $js .=  "f.nomEspecie.value = \"".$nomEspecieC."\";\n";
                        } else {
                            $selected = "";
                        }

                        $nomeEspecie = $codEspecieC." - ".$nomEspecieC;

                        $js .= "f.codEspecie.options[$contEsp] = new Option('".$nomeEspecie."','".$codEspecieC."'".$selected."); ";
                        $contEsp++;
                        $dbEsp->vaiProximo();
                    }

                $dbEsp->limpaSelecao();
                $dbEsp->fechaBD();
                }
            }
        }
        } else {
            $js .= "f.codNatureza.selectedIndex = 0;\n";
            $js .= "limpaSelect(f.codGrupo,0); \n";
              $js .= "f.codGrupo.options[0] = new Option('Selecione','xxx');\n";
              $js .= "limpaSelect(f.codEspecie,0); \n";
              $js .= "f.codEspecie.options[0] = new Option('Selecione','xxx');\n";
        }
    break;

    case 'codNatureza':

        if ($codNatureza != "xxx") {

            // recebe o valor do combo Natureza
            $codNaturezam = $valor;

            // verifica o maior valor de Grupos
            $sSQLMaxGrupo = "
                    SELECT
                            max(cod_grupo) as maximo
                    FROM
                            patrimonio.grupo
                    WHERE
                            cod_natureza = '".$codNaturezam."'";

            $maxGrupo = new dataBaseLegado;
            $maxGrupo->abreBD();
            $maxGrupo->abreSelecao($sSQLMaxGrupo);
            $maxGrupo->vaiPrimeiro();

            $tamanhoMaxGr = strlen($maxGrupo->registroCorrente['maximo']);

            // monta o combo de Grupos
            $sSQL = "
                    SELECT
                        cod_grupo, cod_natureza, nom_grupo, max(cod_grupo) as maximo
                    FROM
                         patrimonio.grupo
                    WHERE
                        cod_natureza = '".$codNaturezam."'
                    GROUP
                        by cod_grupo ,cod_natureza, nom_grupo
                    ORDER
                        by nom_grupo";
            $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();

            $cont = 1;

            $js = "";

            $js .= "limpaSelect(f.codGrupo,0); \n";
            $js .= "f.codGrupo.options[0] = new Option('Selecione','xxx'".$default."); \n";

            while (!$dbEmp->eof()) {
                $codGrupof  = trim($dbEmp->pegaCampo("cod_grupo"));
                $nomGrupof  = trim($dbEmp->pegaCampo("nom_grupo"));
                $dbEmp->vaiProximo();

                $nomeGrupo = $codGrupof ." - ".$nomGrupof;

                $js .= "f.codGrupo.options[$cont] = new Option('".addslashes($nomeGrupo)."','".$codGrupof."'".$default."); \n";

                $cont++;
            }

            $js .= "f.codGrupo.options[0].selected = true;\n";
            $js .= "limpaSelect(f.codEspecie,0); \n";
            $js .= "f.codEspecie.options[0] = new Option('Selecione','xxx'".$default."); \n";

            $js .= "if (f.stCodClassificacao) { \n";
            $js .= "	var concatena = ''; \n";
            $js .= "	var zeros = ''; \n";
            $js .= "	var valor = ''; \n";
            $js .= "	f.stCodClassificacao.value = ''; \n";

            $js .= "	if (f.codNatureza.value != 'xxx') {";
            $js .= "			concatena =  f.codNatureza.value; \n";
            $js .= "	} \n";

            $js .= "	if (f.codGrupo.value != 'xxx') {";
            $js .= "		valor =  ".$tamanhoMaxGr." - f.codGrupo.value.length;";
            $js .= "		    for (i = 0; i<valor; i++) {";
            $js .= "		    	zeros = zeros + '0';";
            $js .= "		    }";
            $js .= "			concatena = concatena + '.' + zeros + f.codGrupo.value; \n";
            $js .= "			zeros = ''; \n";
            $js .= "	} \n";

            $js .= "	if (f.codEspecie.value != 'xxx') {";
            $js .= "		valor =  ".$tamanhoMaxEs." - f.codEspecie.value.length;";
            $js .= "		    for (i = 0; i<valor; i++) {";
            $js .= "		    	zeros = zeros + '0';";
            $js .= "		    }";
            $js .= "			concatena = concatena + '.' + zeros + f.codEspecie.value; \n";
            $js .= "			zeros = ''; \n";
            $js .= "	} \n";

            $js .= "	f.stCodClassificacao.value = concatena; \n";
            $js .= "} \n";

            $dbEmp->limpaSelecao();
            $dbEmp->fechaBD();

        } else {
            // limpa combos de selecao de Natureza, Grupo e Especie e o campo de codigos de classificação
            $js  = "limpaSelect(f.codGrupo,0); \n";
            $js .= "f.codGrupo.options[0] = new Option('Selecione','xxx'".$default."); \n";
            $js .= "limpaSelect(f.codEspecie,0); \n";
            $js .= "f.codEspecie.options[0] = new Option('Selecione','xxx'".$default."); \n";
            $js .= "	f.stCodClassificacao.value = ''; \n";

            // limpa listagem de atributos
            $js .= 'd.getElementById("sAtributos").innerHTML = "";';
        }
    break;

    case 'codGrupo':

             // verifica o maior valor de Grupos
            $sSQLMaxGrupo = "
                    SELECT
                            max(cod_grupo) as maximo
                    FROM
                            patrimonio.grupo
                    WHERE
                            cod_natureza = ".$codNatureza;

            $maxGrupo = new dataBaseLegado;
            $maxGrupo->abreBD();
            $maxGrupo->abreSelecao($sSQLMaxGrupo);
            $maxGrupo->vaiPrimeiro();

            $tamanhoMaxGr = strlen($maxGrupo->registroCorrente['maximo']);

        if ($codGrupo != "xxx") {
            // verifica o maior valor  das  Especies
            $sSQLMaxEspecie = "
                    SELECT
                         max(cod_especie) as maximo
                    FROM
                         patrimonio.especie
                    WHERE
                            cod_natureza = ".$codNatureza."
                            AND cod_grupo = ".$codGrupo;

            $maxEspecie = new dataBaseLegado;
            $maxEspecie->abreBD();
            $maxEspecie->abreSelecao($sSQLMaxEspecie);
            $maxEspecie->vaiPrimeiro();

            $tamanhoMaxEs = strlen($maxEspecie->registroCorrente['maximo']);

             // monta o combo de Especies
            $sSQL = "
                    SELECT
                         cod_especie, cod_grupo, cod_natureza, nom_especie
                    FROM
                         patrimonio.especie
                    WHERE
                            cod_natureza = '".$codNatureza."'
                        AND cod_grupo = '".$codGrupo."'
                    ORDER
                        by nom_especie ";

            $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();

            $cont = 1;

            $js = "";
            $js .= "limpaSelect(f.codEspecie,0); \n";
            $js .= "f.codEspecie.options[0] = new Option('Selecione','xxx'".$default."); \n";

            while (!$dbEmp->eof()) {
                $codEspecief  = trim($dbEmp->pegaCampo("cod_especie"));
                $nomEspecief  = str_replace('\'','\\\'',trim($dbEmp->pegaCampo("nom_especie")));

                $nomeEspecie = $codEspecief." - ".$nomEspecief;

                $dbEmp->vaiProximo();

                $js .= "f.codEspecie.options[$cont] = new Option('".$nomeEspecie."','".$codEspecief."'".$default."); ";

                $cont++;
            }

            $js .= "f.codEspecie.options[0].selected = true; \n";

            $js .= "if (f.stCodClassificacao) { \n";
            $js .= "	var concatena = ''; \n";
            $js .= "	var zeros = ''; \n";
            $js .= "	var valor = ''; \n";
            $js .= "	f.stCodClassificacao.value = ''; \n";

            $js .= "	if (f.codNatureza.value != 'xxx') {";
            $js .= "			concatena =  f.codNatureza.value; \n";
            $js .= "	} \n";

            $js .= "	if (f.codGrupo.value != 'xxx') {";
            $js .= "		valor =  ".$tamanhoMaxGr." - f.codGrupo.value.length;";
            $js .= "		    for (i = 0; i<valor; i++) {";
            $js .= "		    	zeros = zeros + '0';";
            $js .= "		    }";
            $js .= "			concatena = concatena + '.' + zeros + f.codGrupo.value; \n";
            $js .= "			zeros = ''; \n";
            $js .= "	} \n";

            $js .= "	if (f.codEspecie.value != 'xxx') {";
            $js .= "		valor =  ".$tamanhoMaxEs." - f.codEspecie.value.length;";
            $js .= "		    for (i = 0; i<valor; i++) {";
            $js .= "		    	zeros = zeros + '0';";
            $js .= "		    }";
            $js .= "			concatena = concatena + '.' + zeros + f.codEspecie.value; \n";
            $js .= "			zeros = ''; \n";
            $js .= "	} \n";

            $js .= "	f.stCodClassificacao.value = concatena; \n";
            $js .= "} \n";

            $dbEmp->limpaSelecao();
            $dbEmp->fechaBD();

        } else {

            // limpa combos de Selecao de Especie
            $js = "limpaSelect(f.codEspecie,0); \n";
            $js .= "f.codEspecie.options[0] = new Option('Selecione','xxx'".$default."); \n";

            // limpa o campo text codClassificação para ficar apenas com o codigo da natureza
            $js .= "if (f.stCodClassificacao) { \n";
            $js .= "	var concatena = ''; \n";
            $js .= "	var zeros = ''; \n";
            $js .= "	var valor = ''; \n";
            $js .= "	f.stCodClassificacao.value = ''; \n";

            $js .= "	if (f.codNatureza.value != 'xxx') {";
            $js .= "			concatena =  f.codNatureza.value; \n";
            $js .= "	} \n";
            $js .= "} \n";
            $js .= "	f.stCodClassificacao.value = concatena; \n";
            // limpa listagem de atributos
            $js .= 'd.getElementById("sAtributos").innerHTML = "";';
        }

    break;

    case 'codEspecie':

             // verifica o maior valor de Grupos
            $sSQLMaxGrupo = "
                    SELECT
                            max(cod_grupo) as maximo
                    FROM
                            patrimonio.grupo
                    WHERE
                            cod_natureza = ".$codNatureza;

            $maxGrupo = new dataBaseLegado;
            $maxGrupo->abreBD();
            $maxGrupo->abreSelecao($sSQLMaxGrupo);
            $maxGrupo->vaiPrimeiro();

            $tamanhoMaxGr = strlen($maxGrupo->registroCorrente['maximo']);

            // verifica o maior valor  das  Especies
            $sSQLMaxEspecie = "
                    SELECT
                         max(cod_especie) as maximo
                    FROM
                         patrimonio.especie
                    WHERE
                            cod_natureza = ".$codNatureza."
                            AND cod_grupo = ".$codGrupo;

            $maxEspecie = new dataBaseLegado;
            $maxEspecie->abreBD();
            $maxEspecie->abreSelecao($sSQLMaxEspecie);
            $maxEspecie->vaiPrimeiro();

            $tamanhoMaxEs = strlen($maxEspecie->registroCorrente['maximo']);

            $js .= "if (f.stCodClassificacao) { \n";
            $js .= "	var concatena = ''; \n";
            $js .= "	var zeros = ''; \n";
            $js .= "	var valor = ''; \n";
            $js .= "	f.stCodClassificacao.value = ''; \n";

            $js .= "	if (f.codNatureza.value != 'xxx') {";
            $js .= "			concatena =  f.codNatureza.value; \n";
            $js .= "	} \n";

            $js .= "	if (f.codGrupo.value != 'xxx') {";
            $js .= "		valor =  ".$tamanhoMaxGr." - f.codGrupo.value.length;";
            $js .= "		    for (i = 0; i<valor; i++) {";
            $js .= "		    	zeros = zeros + '0';";
            $js .= "		    }";
            $js .= "			concatena = concatena + '.' + zeros + f.codGrupo.value; \n";
            $js .= "			zeros = ''; \n";
            $js .= "	} \n";

            $js .= "	if (f.codEspecie.value != 'xxx') {";
            $js .= "		valor =  ".$tamanhoMaxEs." - f.codEspecie.value.length;";
            $js .= "		    for (i = 0; i<valor; i++) {";
            $js .= "		    	zeros = zeros + '0';";
            $js .= "		    }";
            $js .= "			concatena = concatena + '.' + zeros + f.codEspecie.value; \n";
            $js .= "			zeros = ''; \n";
            $js .= "	}  \n";

            $js .= "	f.stCodClassificacao.value = concatena; \n";
            $js .= "} \n";

    break;

    case 'ordenacao':

        $where="";
        if ($ordenacao != '') {
             // monta o combo de Especies
            $where ="where";
            for ($icount=0;$icount <=$_POST['cont'] + 1;$icount++) {
                $objeto = $_POST["boAtributoDinamico".$icount];
                if ($_POST['boAtributoDinamico'.$icount.'']) {
                    if ($where!='where') {
                        $where .= "  or ";
                    } else {
                        $where .= "  ( ";
                    }
                    $where .="  cod_atributo = ".$objeto;
                }
            }

            if (trim($where) != 'where') {
                $where .=")";
                $where .="and cod_modulo=".$_REQUEST['inModulo'];
            }

            if ($where=='where') {
               $js = "limpaSelect(f.ordenacao,0); \n";
               $js .= "f.ordenacao.options[0] = new Option('Selecione','xxx'".$default."); \n";
               $js .= "limpaSelect(f.filtro,0); \n";
               $js .= "f.filtro.options[0] = new Option('Selecione','xxx'".$default."); \n";
               break;
            }

           $sSQL = "
                 SELECT
                    cod_atributo, nom_atributo
                 FROM
                    administracao.atributo_dinamico  ".$where;

            $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();

            $cont = 1;
            $js = " tamO = f.ordenacao.options.length;";
            $js .= " tamF = f.filtro.options.length;";
            $js .= "f.ordenacao.options[0] = new Option('Selecione','xxx'".$default."); \n";
            $js .= "f.filtro.options[0] = new Option('Selecione','xxx'".$default."); \n";

            while (!$dbEmp->eof()) {
                $codEspecief  = trim($dbEmp->pegaCampo("cod_atributo"));
                $nomEspecief  = trim($dbEmp->pegaCampo("nom_atributo"));

                $dbEmp->vaiProximo();

                $js .= "f.ordenacao.options[tamO] = new Option('".addslashes($nomEspecief)."','".$codEspecief."'".$default."); ";
                $js .= "f.filtro.options[tamF] = new Option('".addslashes($nomEspecief)."','".$codEspecief."'".$default."); ";

                $cont++;
            }

            $js .= "f.ordenacao.options[0].selected = true; \n";
            $js .= "f.filtro.options[0].selected = true; \n";

            $dbEmp->limpaSelecao();
            $dbEmp->fechaBD();

        } else {

            // limpa combos de Selecao de Especie
            $js = "limpaSelect(f.ordenacao,0); \n";
            $js .= "f.ordenacao.options[0] = new Option('Selecione','xxx'".$default."); \n";
            $js .= "limpaSelect(f.filtro,0); \n";
            $js .= "f.filtro.options[0] = new Option('Selecione','xxx'".$default."); \n";
        }
    break;

    case 'filtro':
        $where="";
        if ($filtro != '') {
         // monta o combo de Especies
        $where ="where";
        for ($icount=0;$icount <=$_POST['cont'];$icount++) {
          if ($_POST['boAtributoDinamico'.$icount.'']) {
             if ($where!='where') {
                $where .= "  or ";
              };
             $where .="  cod_atributo = '$icount'";
          }
        }

        if ($where=='where') {
           $js = "limpaSelect(f.ordenacao,0); \n";
           $js .= "f.filtro.options[0] = new Option('Selecione','0'".$default."); \n";
           break;
        }

           $sSQL = "
                 SELECT
                    cod_atributo, nom_atributo
                 FROM
                administracao.atributo_dinamico  ".$where;

            $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();

            $cont = 1;

            $js = "";
            $js .= "limpaSelect(f.filtro,0); \n";
            $js .= "f.filtro.options[0] = new Option('Selecione','0'".$default."); \n";

            while (!$dbEmp->eof()) {
                $codEspecief  = trim($dbEmp->pegaCampo("cod_atributo"));
                $nomEspecief  = trim($dbEmp->pegaCampo("nom_atributo"));

                $dbEmp->vaiProximo();

                $js .= "f.filtro.options[$cont] = new Option('".addslashes($nomEspecief)."','".$codEspecief."'".$default."); ";

                $cont++;
            }

            $js .= "f.filtro.options[0].selected = true; \n";

            $dbEmp->limpaSelecao();
            $dbEmp->fechaBD();

        } else {

            // limpa combos de Selecao de Especie
            $js = "limpaSelect(f.filtro,0); \n";
            $js .= "f.filtro.options[0] = new Option('Selecione','0'".$default."); \n";
        }

    break;
}

executaFrameOculto($js);

?>

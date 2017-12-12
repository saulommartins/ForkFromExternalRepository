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

class configuracaoLegado
{
/**************************************************************************/
/**** Declaração da  variáveis                                          ***/
/**************************************************************************/
    public $comboEstado;
    public $estadoAtual;
    public $chave;
    public $valor;
    public $codOrgao;
    public $nomOrgao;
    public $AnoexercicioOrgao;
    public $comboOrgao;
    public $codUnidade;
    public $nomUnidade;
    public $comboUnidade;
    public $codDepartamento;
    public $nomDepartamento;
    public $comboDepartamento;
    public $modulo;
    public $responsavel;
    public $comboSetor;
    public $codLocal;
    public $nomLocal;
    public $codFuncao;
    public $nomFuncao;
    public $mascaraSetor;
    public $mascaraLocal;
    public $codEstrutural;
    public $stErro;

/**************************************************************************/
/**** Método  Construtor                                                ***/
/**************************************************************************/
    public function configuracaoLegado()
    {
        $this->comboEstado = "";
        $this->estadoAtual = "";
        $this->chave = "";
        $this->valor = "";
        $this->codOrgao = "";
        $this->nomOrgao = "";
        $this->AnoexercicioOrgao = "";
        $this->comboOrgao = "";
        $this->codUnidade = "";
        $this->nomUnidade = "";
        $this->comboUnidade  = "";
        $this->codDepartamento = "";
        $this->nomDepartamento = "";
        $this->comboDepartamento = "";
        $this->codSetor = "";
        $this->nomSetor = "";
        $this->modulo = "";
        $this->responsavel = "";
        $this->comboSetor = "";
        $this->codLocal = "";
        $this->nomLocal = "";
        $this->codFuncao = "";
        $this->nomFuncao = "";
        $this->mascaraSetor = "";
        $this->mascaraLocal = "";
        $this->codEstrutural = "";
        }

/**************************************************************************/
/**** Pega as variáveis do estado atual                                 ***/
/**************************************************************************/
    public function setaEstadoAtual($cod_uf)
    {
        $this->estadoAtual = $cod_uf;
    }

/**************************************************************************/
/**** Gera o Combo com os Estados para seleção                          ***/
/**************************************************************************/
    public function listaComboEstados()
    {
        $sSQL = "SELECT * FROM sw_uf ORDER by nom_uf";
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $this->comboEstado = "";
        $this->comboEstado .= "<select name=cod_uf onChange='javascript:document.frm.submit();'>\n";
        while (!$dbEmp->eof()) {
            $cod_uf  = trim($dbEmp->pegaCampo("cod_uf"));
            $nom_uf  = trim($dbEmp->pegaCampo("nom_uf"));
            $dbEmp->vaiProximo();
            $this->comboEstado .= "<option value=".$cod_uf;
            $this->comboEstado .=">".$nom_uf."</option>\n";
    }
        $this->comboEstado .= "</select>";
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
    }

/**************************************************************************/
/**** Gera o Combo com os Municipios para seleção                          ***/
/**************************************************************************/
    public function listaComboMunicipios()
    {
        //$sSQL = "SELECT * FROM sw_municipio WHERE cod_uf = ".$vcodUf." ORDER BY nom_municipio";
        $sSQL = "SELECT * FROM sw_municipio WHERE cod_uf = ".$this->estadoAtual." ORDER BY nom_municipio";

        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $this->comboMunicipio = "";
        $this->comboMunicipio .= "<select name=cod_municipio onChange='javascript:document.frm.submit();'>\n";
        while (!$dbEmp->eof()) {
            $cod_municipio  = trim($dbEmp->pegaCampo("cod_municipio"));
            $nom_municipio  = trim($dbEmp->pegaCampo("nom_municipio"));
            $dbEmp->vaiProximo();
            $this->comboMunicipio .= "<option value=".$cod_municipio;
            $this->comboMunicipio .=">".$nom_municipio."</option>\n";
    }
        $this->comboMunicipio .= "</select>";
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
    }

/**************************************************************************/
/**** Mostra o na tela o Combo por Estado gerado                        ***/
/**************************************************************************/
    public function mostraComboEstado()
    {
        echo $this->comboEstado;
    }

/**************************************************************************/
/**** Pega as variáveis do valor e da chave para update                 ***/
/**************************************************************************/
    public function setaChaveValor($chave,$valor,$modulo=2,$exercicio="")
    {
        $this->chave = $chave;
        $this->valor = $valor;
        $this->modulo = $modulo;
        $this->AnoexercicioOrgao = $exercicio;
    }

/**************************************************************************/
/**** Executa o UPDATE na tabela de Coonfiguração                       ***/
/**************************************************************************/
    public function updateConfiguracao()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $insert = "UPDATE administracao.configuracao SET valor = '$this->valor' WHERE parametro = '$this->chave' AND cod_modulo = $this->modulo";
        if ($this->AnoexercicioOrgao != "")
            $insert .= " AND exercicio = '".$this->AnoexercicioOrgao."'";
        if ($dbConfig->executaSql($insert))
            $ok = true;
        else
            $ok = false;
        $dbConfig->fechaBd();

        return $ok;
    }
//fim do updateConfiguracao
/**************************************************************************/
/**** Executa o UPDATE na tabela de Coonfiguração                       ***/
/**************************************************************************/
    public function updateConfiguracaoNovo()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $insert = "INSERT INTO administracao.configuracao (parametro,valor,exercicio,cod_modulo) VALUES ('".$this->chave."','".$this->valor."','".$this->AnoexercicioOrgao."','".$this->modulo."')";
        if ($dbConfig->executaSql($insert))
            $ok = true;
        else
            $ok = false;
        $dbConfig->fechaBd();

        return $ok;
    }
//fim do updateConfiguracao

/**************************************************************************/
/**** Mostra nome do Estado atual                                       ***/
/**************************************************************************/
    public function nomeEstado()
    {
        $sSQL = "SELECT * FROM sw_uf WHERE cod_uf = $this->estadoAtual";
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $nomeEstado = "";
        while (!$dbEmp->eof()) {
            $cod_uf  = trim($dbEmp->pegaCampo("cod_uf"));
            $nom_uf  = trim($dbEmp->pegaCampo("nom_uf"));
            $dbEmp->vaiProximo();
            $nomeEstado .= $nom_uf;
    }
    $dbEmp->limpaSelecao();
    $dbEmp->fechaBD();
    echo "$nomeEstado";
    }

/**************************************************************************/
/**** Pega as variáveis para inserir Órgão                              ***/
/**************************************************************************/
    public function setaValorOrgao($codOrgao,$nomOrgao="",$AnoExercicio="",$responsavel="")
    {
        $this->codOrgao = $codOrgao;
        $this->nomOrgao = $nomOrgao;
        $this->AnoexercicioOrgao = $AnoExercicio;
        $this->responsavel = $responsavel;
        }

/**************************************************************************/
/**** Executa o UPDATE na tabela de Coonfiguração                       ***/
/**************************************************************************/
    public function insertOrgao()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        if ($this->responsavel == "") {
            $this->responsavel = 0;
        }
        $insert = "INSERT INTO administracao.orgao (cod_orgao, nom_orgao, ano_exercicio, usuario_responsavel) VALUES ('".$this->codOrgao."','".$this->nomOrgao."','".$this->AnoexercicioOrgao."','".$this->responsavel."')";
        //echo $insert."<br>\n";
        if ($dbConfig->executaSql($insert))
            $ok = true;
        else
            $ok = false;
        $dbConfig->fechaBd();

        return $ok;
    }

/**************************************************************************/
/**** Executa o UPDATE na tabela de Coonfiguração                       ***/
/**************************************************************************/
    public function updateOrgao()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        if ($this->responsavel == "") {
            $this->responsavel = 0;
        }
        $insert = "UPDATE administracao.orgao SET nom_orgao = '".$this->nomOrgao."', usuario_responsavel='".$this->responsavel."' WHERE cod_orgao = ".$this->codOrgao." AND ano_exercicio = '".$this->AnoexercicioOrgao."'";
        if ($dbConfig->executaSql($insert))
            $ok = true;
        else
            $ok = false;
        $dbConfig->fechaBd();

        return $ok;
    }

/**************************************************************************/
/**** Executa o UPDATE na tabela de Coonfiguração                       ***/
/**************************************************************************/
    public function deleteOrgao()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $insert = "DELETE FROM administracao.orgao WHERE cod_orgao = ".$this->codOrgao." AND ano_exercicio = '".$this->AnoexercicioOrgao."'";
        if ($dbConfig->executaSql($insert))
            $ok = true;
        else{
            $this->stErro = $dbConfig->pegaUltimoErro();
            $ok = false;
        }
        $dbConfig->fechaBd();

        return $ok;
    }//fim do deleteOrgao

/**************************************************************************/
/**** Gera o Combo com os Órgão para seleção                            ***/
/**************************************************************************/
    public function listaComboOrgaos()
    {
        $sSQL = "SELECT * FROM administracao.orgao ORDER by nom_orgao";
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $this->comboOrgao = "";
        $this->comboOrgao .= "<select name=codOrgao>\n<option value=xxx SELECTED>Selecione</option>\n";
        while (!$dbEmp->eof()) {
            $codOrgao  = trim($dbEmp->pegaCampo("cod_orgao"));
            $nomOrgao  = trim($dbEmp->pegaCampo("nom_orgao"));
            $anoEf  = trim($dbEmp->pegaCampo("ano_exercicio"));
            $dbEmp->vaiProximo();
            $this->comboOrgao .= "<option value=".$codOrgao.">".$nomOrgao." - ".$anoEf."</option>\n";
        }
        $this->comboOrgao .= "</select>";
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
    }

/**************************************************************************/
/**** Mostra o na tela o Combo por orgaos gerado                        ***/
/**************************************************************************/
    public function mostraComboOrgaos()
    {
        echo $this->comboOrgao;
    }

/**************************************************************************/
/**** Pega as variáveis para inserir Unidade                            ***/
/**************************************************************************/
    public function setaValorUnidade($codUnidade,$codOrgao="",$nomUnidade="",$AnoExercicio="",$responsavel="")
    {
        $this->codUnidade = $codUnidade;
        $this->codOrgao = $codOrgao;
        $this->nomUnidade = $nomUnidade;
        $this->AnoexercicioOrgao = $AnoExercicio;
        $this->responsavel = $responsavel;
    }

/**************************************************************************/
/**** Executa o INSERT na tabela de Configuração de UNIDADES            ***/
/**************************************************************************/
    public function insertUnidade()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        if ($this->responsavel == "") {
            $this->responsavel = 0;
        }
        $insert = "INSERT INTO administracao.unidade (cod_unidade, cod_orgao, nom_unidade, ano_exercicio,usuario_responsavel) VALUES ('".$this->codUnidade."','".$this->codOrgao."','".$this->nomUnidade."','".$this->AnoexercicioOrgao."','".$this->responsavel."')";
        if ($dbConfig->executaSql($insert))
            $ok = true;
        else
            $ok = false;
        $dbConfig->fechaBd();

        return $ok;
    }

/**************************************************************************/
/**** Executa o UPDATE  na tabela de Configuração de UNIDADES           ***/
/**************************************************************************/
    public function updateUnidade()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        if ($this->responsavel == "") {
            $this->responsavel = 0;
        }
        $insert = "UPDATE administracao.unidade SET nom_unidade  = '".$this->nomUnidade."', usuario_responsavel='".$this->responsavel."' WHERE cod_unidade = ".$this->codUnidade." AND cod_orgao = ".$this->codOrgao." AND ano_exercicio = '".$this->AnoexercicioOrgao."'";
        //echo $insert."<br>";
        if ($dbConfig->executaSql($insert))
            $ok =  true;
        else
            $ok = false;
        $dbConfig->fechaBd();

        return $ok;
    }

/**************************************************************************/
/**** Executa o UPDATE na tabela de Coonfiguração                       ***/
/**************************************************************************/
    public function deleteUnidade()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $insert = "DELETE FROM administracao.unidade WHERE cod_orgao = ".$this->codOrgao." AND cod_unidade = ".$this->codUnidade." AND ano_exercicio = '".$this->AnoexercicioOrgao."'";
        if ($dbConfig->executaSql($insert))
            $ok = true;
        else{
            $this->stErro = $dbConfig->pegaUltimoErro();
            $ok = false;
        }
        $dbConfig->fechaBd();

        return $ok;
    }

/**************************************************************************/
/**** Pega as variáveis para combo de Unidade                           ***/
/**************************************************************************/
    public function setaValorComboUnidades($codOrgao,$AnoExercicio)
    {
        $this->codOrgao = $codOrgao;
        $this->AnoexercicioOrgao = $AnoExercicio;
    }

/**************************************************************************/
/**** Gera o Combo com os unidade para seleção                            ***/
/**************************************************************************/
    public function listaComboUnidades()
    {
        $sSQL = "SELECT * FROM administracao.unidade WHERE cod_orgao = ".$this->codOrgao." AND ano_exercicio = '".$this->AnoexercicioOrgao."' ORDER by nom_unidade";
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $this->comboUnidade = "";
        $this->comboUnidade .= "<select name=codUnidade>\n<option value=xxx SELECTED>Selecione</option>\n";
        while (!$dbEmp->eof()) {
            $codUnidade  = trim($dbEmp->pegaCampo("cod_unidade"));
            $nomUnidade  = trim($dbEmp->pegaCampo("nom_unidade"));
            $anoEf  = trim($dbEmp->pegaCampo("ano_exercicio"));
            $dbEmp->vaiProximo();
            $this->comboUnidade .= "<option value=".$codUnidade.">".$nomUnidade." - ".$anoEf."</option>\n";
        }
        $this->comboUnidade .= "</select>";
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
    }
/**************************************************************************/
/**** Mostra o na tela o Combo por unidade gerado                        ***/
/**************************************************************************/
    public function mostraComboUnidade()
    {
        echo $this->comboUnidade;
    }

/**************************************************************************/
/**** Pega as variáveis para inserir Departamento                       ***/
/**************************************************************************/
    public function setaValorDepartamento($codDepartamento,$codUnidade="",$codOrgao="",$nomDepartamento="",$AnoExercicio="",$responsavel="")
    {
        $this->codDepartamento = $codDepartamento;
        $this->codUnidade = $codUnidade;
        $this->codOrgao = $codOrgao;
        $this->nomDepartamento = $nomDepartamento;
        $this->AnoexercicioOrgao = $AnoExercicio;
        $this->responsavel = $responsavel;
    }

/**************************************************************************/
/**** Executa o INSERT na tabela de Configuração de DEPARTAMENTOS       ***/
/**************************************************************************/
    public function insertDepartamento()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        if ($this->responsavel == "") {
            $this->responsavel = 0;
        }
        $insert = "INSERT INTO administracao.departamento (cod_departamento, cod_unidade, cod_orgao, nom_departamento, ano_exercicio, usuario_responsavel) VALUES ('".$this->codDepartamento."','".$this->codUnidade."','".$this->codOrgao."','".$this->nomDepartamento."','".$this->AnoexercicioOrgao."','".$this->responsavel."')";
        if ($dbConfig->executaSql($insert))
            $ok =  true;
        else
            $ok = false;
        $dbConfig->fechaBd();

        return $ok;
    }

/**************************************************************************/
/**** Executa o UPDATE  na tabela de Configuração de DEPARTAMENTOS      ***/
/**************************************************************************/
    public function updateDepartamento()
    {
        $db = new dataBaseLegado;
        $db->abreBd();
        if ($this->responsavel == "") {
            $this->responsavel = 0;
        }
        $sql = "UPDATE administracao.departamento
                SET nom_departamento  = '".$this->nomDepartamento."',
                    usuario_responsavel='".$this->responsavel."'
                WHERE cod_departamento = ".$this->codDepartamento."
                AND cod_unidade = ".$this->codUnidade."
                AND cod_orgao = ".$this->codOrgao."
                AND ano_exercicio = '".$this->AnoexercicioOrgao."'";
        if ($db->executaSql($sql)) {
            $ok = true;
        } else {
            $ok = false;
            echo $sql;
        }
        $db->fechaBd();

        return $ok;
    }

/**************************************************************************/
/**** Executa o DELETE na tabela de Coonfiguração em DEPARTAMENTOS      ***/
/**************************************************************************/
    public function deleteDepartamento()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $insert = "DELETE FROM administracao.departamento WHERE cod_departamento = ".$this->codDepartamento." AND cod_unidade = ".$this->codUnidade." AND cod_orgao = ".$this->codOrgao." AND ano_exercicio = '".$this->AnoexercicioOrgao."'";
        if ($dbConfig->executaSql($insert))
            $ok = true;
        else{
            $this->stErro = $dbConfig->pegaUltimoErro();
            $ok = false;
        }
        $dbConfig->fechaBd();

        return $ok;
    }

/**************************************************************************/
/**** Pega as variáveis para combo de DEPARTAMENTOS                     ***/
/**************************************************************************/
    public function setaValorComboDepartamentos($codOrgao,$codUnidade,$AnoExercicio)
    {
        $this->codOrgao = $codOrgao;
        $this->codUnidade = $codUnidade;
        $this->AnoexercicioOrgao = $AnoExercicio;
    }

/**************************************************************************/
/**** Gera o Combo com os DEPARTAMENTOS para seleção                    ***/
/**************************************************************************/
    public function listaComboDepartamentos()
    {
        $sSQL = "SELECT * FROM administracao.departamento WHERE cod_unidade = ".$this->codUnidade." AND cod_orgao = ".$this->codOrgao." AND ano_exercicio = '".$this->AnoexercicioOrgao."' ORDER by nom_departamento";
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $this->comboDepartamento = "";
        $this->comboDepartamento .= "<select name=codDepartamento>\n<option value=xxx SELECTED>Selecione</option>\n";
        while (!$dbEmp->eof()) {
            $codDepartamento  = trim($dbEmp->pegaCampo("cod_departamento"));
            $nomDepartamento  = trim($dbEmp->pegaCampo("nom_departamento"));
            $anoEf  = trim($dbEmp->pegaCampo("ano_exercicio"));
            $dbEmp->vaiProximo();
            $this->comboDepartamento .= "<option value=".$codDepartamento.">".$nomDepartamento." - ".$anoEf."</option>\n";
        }
        $this->comboDepartamento .= "</select>";
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
    }
/**************************************************************************/
/**** Mostra o na tela o Combo por DEPARTAMENTOS gerado                 ***/
/**************************************************************************/
    public function mostraComboDepartamentos()
    {
        echo $this->comboDepartamento;
    }

/**************************************************************************/
/**** Pega as variáveis para inserir SETORES                            ***/
/**************************************************************************/
    public function setaValorSetor($codSetor,$codDepartamento="",$codUnidade="",$codOrgao="",$nomSetor="",$AnoExercicio="",$responsavel="")
    {
        $this->codSetor = $codSetor;
        $this->codDepartamento = $codDepartamento;
        $this->codUnidade = $codUnidade;
        $this->codOrgao = $codOrgao;
        $this->nomSetor = $nomSetor;
        $this->AnoexercicioOrgao = $AnoExercicio;
        $this->responsavel = $responsavel;
    }

/**************************************************************************/
/**** Executa o INSERT na tabela de Configuração de SETORES             ***/
/**************************************************************************/
    public function insertSetor()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        if ($this->responsavel == "") {
            $this->responsavel = 0;
        }
        $insert = "INSERT INTO administracao.setor (cod_setor, cod_departamento, cod_unidade, cod_orgao, nom_setor, ano_exercicio, usuario_responsavel) VALUES ('".$this->codSetor."','".$this->codDepartamento."','".$this->codUnidade."','".$this->codOrgao."','".$this->nomSetor."','".$this->AnoexercicioOrgao."','".$this->responsavel."')";
        if ($dbConfig->executaSql($insert))
            $ok = true;
        else
            $ok = false;
        $dbConfig->fechaBd();

        return $ok;
    }

/**************************************************************************/
/**** Executa o UPDATE  na tabela de Configuração de SETORES            ***/
/**************************************************************************/
    public function updateSetor()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        if ($this->responsavel == "") {
            $this->responsavel = 0;
        }
        $insert = "UPDATE administracao.setor SET nom_setor  = '".$this->nomSetor."', usuario_responsavel='".$this->responsavel."' WHERE cod_setor = ".$this->codSetor." AND cod_departamento = ".$this->codDepartamento." AND cod_unidade = ".$this->codUnidade." AND cod_orgao = ".$this->codOrgao." AND ano_exercicio = '".$this->AnoexercicioOrgao."'";
        if ($dbConfig->executaSql($insert))
            $ok = true;
        else
            $ok = false;
        $dbConfig->fechaBd();

        return $ok;
    }

/**************************************************************************/
/**** Executa o DELETE na tabela de Coonfiguração em SETOR              ***/
/**************************************************************************/
    public function deleteSetor()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $insert = "DELETE FROM administracao.setor WHERE cod_setor = ".$this->codSetor." AND cod_departamento = ".$this->codDepartamento." AND cod_unidade = ".$this->codUnidade." AND cod_orgao = ".$this->codOrgao." AND ano_exercicio = '".$this->AnoexercicioOrgao."'";
        if ($dbConfig->executaSql($insert))
            $ok = true;
        else{
            $this->stErro = $dbConfig->pegaUltimoErro();
            $ok = false;
        }
        $dbConfig->fechaBd();

        return $ok;
    }

/**************************************************************************/
/**** Pega as variáveis para combo de SETORES                            ***/
/**************************************************************************/
    public function setaValorComboSetor($codDepartamento, $codOrgao,$codUnidade,$AnoExercicio)
    {
        $this->codDepartamento  = $codDepartamento;
        $this->codOrgao = $codOrgao;
        $this->codUnidade = $codUnidade;
        $this->AnoexercicioOrgao = $AnoExercicio;
    }

/**************************************************************************/
/**** Gera o Combo com os DEPARTAMENTOS para seleção                    ***/
/**************************************************************************/
    public function listaComboSetor()
    {
        $sSQL = "SELECT * FROM administracao.setor WHERE cod_departamento = ".$this->codDepartamento." AND cod_unidade = ".$this->codUnidade." AND cod_orgao = ".$this->codOrgao." AND ano_exercicio = '".$this->AnoexercicioOrgao."' ORDER by nom_setor";
        //print $sSQL;
        $dbEmp = new dataBaseLegado;
        $dbEmp->abreBD();
        $dbEmp->abreSelecao($sSQL);
        $dbEmp->vaiPrimeiro();
        $this->comboSetor = "";
        $this->comboSetor .= "<select name=codSetor>\n<option value=xxx SELECTED>Selecione</option>\n";
        while (!$dbEmp->eof()) {
            $codSetor = trim($dbEmp->pegaCampo("cod_setor"));
            $nomSetor = trim($dbEmp->pegaCampo("nom_setor"));
            $anoEf  = trim($dbEmp->pegaCampo("ano_exercicio"));
            $dbEmp->vaiProximo();
            $this->comboSetor .= "<option value=".$codSetor.">".$nomSetor." - ".$anoEf."</option>\n";
        }
        $this->comboSetor .= "</select>";
        $dbEmp->limpaSelecao();
        $dbEmp->fechaBD();
    }

/**************************************************************************/
/**** Mostra o na tela o Combo por DEPARTAMENTOS gerado                 ***/
/**************************************************************************/
    public function mostraComboSetor()
    {
        echo $this->comboSetor;
    }

/**************************************************************************/
/**** Pega as variáveis para inserir LOCAL                              ***/
/**************************************************************************/
    public function setaValorLocal($codLocal,$codSetor="",$codDepartamento="",$codUnidade="",$codOrgao="",$nomLocal="",$AnoExercicio="",$responsavel="")
    {
        $this->codLocal = $codLocal;
        $this->codSetor = $codSetor;
        $this->codDepartamento = $codDepartamento;
        $this->codUnidade = $codUnidade;
        $this->codOrgao = $codOrgao;
        $this->nomLocal = $nomLocal;
        $this->AnoexercicioOrgao = $AnoExercicio;
        $this->responsavel = $responsavel;
    }

/**************************************************************************/
/**** Executa o INSERT na tabela de Configuração de LOCAL               ***/
/**************************************************************************/
    public function insertLocal()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        if ($this->responsavel == "") {
            $this->responsavel = 0;
        }
        $insert = "INSERT INTO administracao.local (cod_local, cod_setor, cod_departamento, cod_unidade, cod_orgao, nom_local, ano_exercicio, usuario_responsavel) VALUES ('".$this->codLocal."','".$this->codSetor."','".$this->codDepartamento."','".$this->codUnidade."','".$this->codOrgao."','".$this->nomLocal."','".$this->AnoexercicioOrgao."','".$this->responsavel."')";
        //print $insert;
        if ($dbConfig->executaSql($insert))
            $ok = true;
        else
            $ok = false;
        $dbConfig->fechaBd();

        return $ok;
    }

/**************************************************************************/
/**** Executa o UPDATE  na tabela de Configuração de LOCAL              ***/
/**************************************************************************/
    public function updateLocal()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        if ($this->responsavel == "") {
            $this->responsavel = 0;
        }
        $insert = "UPDATE administracao.local SET nom_local  = '".$this->nomLocal."', usuario_responsavel='".$this->responsavel."' WHERE cod_local = ".$this->codLocal." AND cod_setor = ".$this->codSetor." AND cod_departamento = ".$this->codDepartamento." AND cod_unidade = ".$this->codUnidade." AND cod_orgao = ".$this->codOrgao." AND ano_exercicio = '".$this->AnoexercicioOrgao."'";
        //print $insert;
        if ($dbConfig->executaSql($insert))
            $ok = true;
        else
            $ok = false;
        $dbConfig->fechaBd();

        return $ok;
    }

/**************************************************************************/
/**** Executa o DELETE na tabela de Configuração em LOCAL               ***/
/**************************************************************************/
    public function deleteLocal()
    {
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $insert = "DELETE FROM administracao.local WHERE cod_local = ".$this->codLocal." AND cod_setor = ".$this->codSetor." AND cod_departamento = ".$this->codDepartamento." AND cod_unidade = ".$this->codUnidade." AND cod_orgao = ".$this->codOrgao." AND ano_exercicio = '".$this->AnoexercicioOrgao."'";
        //print $insert;
        if ($dbConfig->executaSql($insert))
            $ok = true;
        else{
            $this->stErro = $dbConfig->pegaUltimoErro();
            $ok = false;
        }
        $dbConfig->fechaBd();

        return $ok;
    }

/**************************************************************************/
/**** Executa o UPDATE  na tabela de Configuração de Conta Permanente   ***/
/**************************************************************************/
    public function updateContaPermanente($codEstrutural,$exercicio)
    {
        $this->codEstrutural = $codEstrutural;
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $insert = "UPDATE administracao.configuracao SET valor = '".$this->codEstrutural."' where cod_modulo = 6 and parametro = 'grupo_contas_permanente' and exercicio = ".$exercicio."";
        if ($dbConfig->executaSql($insert))
            $ok = true;
        else
            $ok = false;
        $dbConfig->fechaBd();

        return $ok;
    }

}//Fim da classe
?>

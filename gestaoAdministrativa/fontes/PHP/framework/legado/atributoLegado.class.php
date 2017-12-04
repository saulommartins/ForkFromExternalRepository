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

class atributoLegado
{
/**************************************************************************
 Variáveis da classe
***************************************************************************/
    public $codAtributo;
    public $nomAtributo;
    public $tipoValor;
    public $valorPadrao;
    public $tabela;

/**************************************************************************
 Método construtor: inicializa as variáveis com valor nulo
***************************************************************************/
    public function atributoLegado()
    {
        $this->codAtributo = "";
        $this->nomAtributo = "";
        $this->tipoValor = "";
        $this->valorPadrao = "";
        $this->tabela = "";
    }

/**************************************************************************
 Atribui valores às variáveis da classe.
 Se o nome ($variavel) informado corresponder a uma variável de classe, grava
 o valor ($valor) informado nesta variável
 Exemplo:
    Entrada:setaVariaveis("nomAtributo","teste")
    Saída: $this->nomAtributo = "teste"
 Se a variável passada é um vetor, abre o vetor atribuindo os valores às
 variáveis correspondentes com a chave do vetor.
 Exemplo:
    Entrada: $vetor = array(nomeVariavel=>"valorVariavel")
             setaVariaveis($vetor)
    Saída: $this->nomeVariavel = valorVariavel
***************************************************************************/
    public function setaVariaveis($variavel,$valor="")
    {
        $retorno = false;
        //Verifica se existe uma variável de classe com o nome fornecido
        if (isset($this->$variavel)) {
            $this->$variavel = $valor;
            $retorno = true;
        } elseif (is_array($variavel)) { //Se for vetor varre as chaves procurando por chaves correspondentes às variáveis
            foreach ($variavel as $chave=>$val) {
                if (isset($this->$chave)) { //Verifica se existe uma variável de classe com o nome fornecido na chave
                    $this->$chave = $val;
                    $retorno = true;
                }
            }
        }

        return $retorno;
    }//Fim do método setaVariaveis

/**************************************************************************
 Inclui um novo atributo na tabela informada
***************************************************************************/
    public function incluirAtributo()
    {
        $this->codAtributo = pegaId("cod_atributo",$this->tabela);
        $db = new databaseLegado;
        $db->abreBd();
        $sql  = "Insert Into ".$this->tabela."
                 (cod_atributo, nom_atributo, tipo, valor_padrao)
                 Values (".$this->codAtributo.", '".$this->nomAtributo."',
                 '".$this->tipoValor."', '".$this->valorPadrao."') ";
        if ($db->executaSql($sql)) {
            $retorno = true;
        } else {
            $retorno = false;
        }
        $db->fechaBd();

        return $retorno;
    }//Fim do método incluirAtributo

/**************************************************************************
 Altera um atributo na tabela informada
***************************************************************************/
    public function alterarAtributo()
    {
        $db = new databaseLegado;
        $db->abreBd();
        $sql  = "Update ".$this->tabela."
                Set nom_atributo = '".$this->nomAtributo."',
                    valor_padrao = '".$this->valorPadrao."'
                Where cod_atributo = ".$this->codAtributo;
        if ($db->executaSql($sql)) {
            $retorno = true;
        } else {
            $retorno = false;
        }
        $db->fechaBd();

        return $retorno;
    }//Fim do método alterarAtributo

/**************************************************************************
 Exclui um atributo na tabela informada
***************************************************************************/
    public function excluirAtributo()
    {
        $db = new databaseLegado;
        $db->abreBd();
        $sql = "Delete From ".$this->tabela."
                Where cod_atributo = ".$this->codAtributo;
        if ($db->executaSql($sql)) {
            $retorno = true;
        } else {
            $retorno = false;
        }
        $db->fechaBd();

        return $retorno;
    }//Fim do método excluirAtributo

/**************************************************************************
 Método para retornar todos os atributos, com opção de filtro e ordenação.
 Caso a variável $codAtributo seja informado o filtro é colocado
 automaticamente.
 Retorna um vetor com um ou mais resultados.
***************************************************************************/
    public function retornaAtributos($codAtributo=0, $condicao="", $ordem="")
    {
        $db = new databaseLegado;
        $db->abreBd();
        $sql  = "Select * From ".$this->tabela."
                 Where cod_atributo > 0 ";
        if ($codAtributo != 0) {
            $sql .= " And cod_atributo = ".$codAtributo." ";
        }
        $sql .= $condicao;
        $sql .= $ordem;
        //print_r($sql);
        $db->abreSelecao($sql);
        $db->fechaBd();
        if ($db->numeroDeLinhas == 1) {
            $this->codAtributo = $db->pegaCampo("cod_atributo");
            $this->nomAtributo = $db->pegaCampo("nom_atributo");
            $this->tipoValor = $db->pegaCampo("tipo");
            $this->valorPadrao = $db->pegaCampo("valor_padrao");
        }
        $vetAtributo = array();
        while ( !$db->eof() ) {
            $cod = $db->pegaCampo("cod_atributo");
            $nom = $db->pegaCampo("nom_atributo");
            $tipo = $db->pegaCampo("tipo");
            $valor = $db->pegaCampo("valor_padrao");
            $arTmp = array( "codAtributo" => $cod,
                            "nomAtributo" => $nom,
                            "tipoValor" => $tipo,
                            "valorPadrao" => $valor);
            $vetAtributo[] = $arTmp;
            $db->vaiProximo();
        }
        $db->limpaSelecao();

        return $vetAtributo;
    }//Fim do método retornaAtributos

    public function validaExcluirAtributo($codAtributo)
    {
        $db = new databaseLegado;
        $db->abreBd();
        $sql  = "Select * From sw_cgm_atributo_valor";
        $sql .= " Where cod_atributo = ".$codAtributo." ";
        $db->abreSelecao($sql);
        $db->fechaBd();
        if ($db->numeroDeLinhas < 1) {
            return true;
        } else {
            return false;
        }
    }

}//Fim da classe atributos

?>

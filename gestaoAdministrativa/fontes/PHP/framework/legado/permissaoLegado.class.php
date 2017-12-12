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

class permissaoLegado
{
    //Declaração de variáveis
    public $codAcao, $codFuncionalidade, $codModulo, $numCgm, $numCgmImportado;

    //Método construtor
    public function permissaoLegado()
    {
        $this->codAcao = 0;
        $this->codFuncionalidade = 0;
        $this->codModulo = 0;
        $this->numCgm = 0;
        $this->numCgmImportado = 0;

    }// Fim do método construtor

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

/***************************************************************************
Método de verificação de permissão de módulo:
Entra com o numcgm e o código do módulo e o exercício para
ver se o usuário tem permissão de acesso ao módulo selecionado
/**************************************************************************/
    public function checaPermissaoModulo($cgm,$codModulo,$exercicio)
    {
        $sSQL = "Select Distinct M.cod_modulo
                From administracao.modulo as M, administracao.funcionalidade as F, administracao.acao as A
                Where F.cod_modulo = M.cod_modulo
                And M.cod_modulo = '".$codModulo."'
                And A.cod_funcionalidade = F.cod_funcionalidade
                And A.cod_acao IN (
                    Select cod_acao
                    From administracao.permissao
                    Where numcgm = $cgm
                    And ano_exercicio = '".$exercicio."' ) ";
        $conectaBD = new databaseLegado;
        $conectaBD->abreBD();
        $conectaBD->abreSelecao($sSQL);
        $conectaBD->vaiPrimeiro();
            if (!$conectaBD->eof()) {
                return true;
            } else {
                return false;
            }
        $conectaBD->limpaSelecao();
        $conectaBD->fechaBD();
    }// Fim da function checaPermissaoModulo

/***************************************************************************
Método de verificação de permissão de ação:
Entra com o numcgm e o código da ação para ver se o usuário
tem permissão de acesso à ação selecionada
/**************************************************************************/
    public function checaPermissaoAcao($cgm,$codAcao,$exercicio)
    {
        $sSQL = "Select numcgm, cod_acao From administracao.permissao
                Where numcgm = '$cgm'
                And cod_acao = '$codAcao'
                And ano_exercicio = '$exercicio' ";
        $conectaBD = new databaseLegado;
        $conectaBD->abreBD();
        $conectaBD->abreSelecao($sSQL);
        $conectaBD->vaiPrimeiro();
            if (!$conectaBD->eof()) {
                return true;
            } else {
                return false;
            }
        $conectaBD->limpaSelecao();
        $conectaBD->fechaBD();
    }// Fim da function checaPermissaoAcao

/***************************************************************************
Método para alterar as permissões de um usuário:
Entra com o cgm, todos os módulos listados, isto é, os módulos que o usuário
atual tem permissão de alterar, e um array com todas as ações listadas
Este método exclui todas as permissões, e logo em seguida
inclui as permissões selecionadas
/**************************************************************************/
    public function alteraPermissao($cgm,$todosModulos,$codAcao,$exercicio)
    {
        //Exclui todas as permissões do usuário dentro dos módulos selecionados
        //** Observe que a query impede que o usuário siam tenha suas permissões excluidas por acidente, declarando "numcgm <> '0'" mesmo que isso não fosse neceessário
        //$sSQL = "Delete From permissao Where numcgm = '".$cgm."' And numcgm <> '0' ";
        $sSQL = "Delete From administracao.permissao
                Where numcgm = '".$cgm."'
                And numcgm <> '0'
                And ano_exercicio = '".$exercicio."'
                And cod_acao IN
                (Select A.cod_acao
                From administracao.acao as A, administracao.funcionalidade as F
                Where A.cod_funcionalidade = F.cod_funcionalidade
                And F.cod_modulo IN (".$todosModulos."))";
        $conectaBD = new databaseLegado;
        $conectaBD->abreBD();
        if ($conectaBD->executaSql($sSQL)) {
            $ok = true;
            //echo $sSQL."<br><br>";
        } else {
            $ok = false;
        }
        $conectaBD->fechaBD();

        $sSQL = "";
            if (count($codAcao) > 0) {
                foreach ($codAcao as $value) {
                    $sSQL .= "Insert Into administracao.permissao(
                              numcgm, cod_acao, ano_exercicio
                              ) Values('".$cgm."','".$value."', '".$exercicio."' ); ";
                }
            }
        if ($sSQL != "") { //Se o array codAcao estiver vazio o usuário não terá nenhuma ação adicionada às suas permissões
            $conectaBD = new databaseLegado;
            $conectaBD->abreBD();
            if ($conectaBD->executaSql($sSQL)) {
                $ok = true;
            } else {
                $ok = false;
            }
            $conectaBD->fechaBD();

            return true;
        } else {
            return false;
        }
    }// Fim function alteraPermissao

/***************************************************************************
 Este método copia as permissões de um usuário para outro
 Obs: Todas as permissões serão excluídas independente do exercício e
      todas as permissões serão copiadas independente do exercício
/**************************************************************************/
    public function importarPermissao()
    {
        $sql = "";
        //Remove todas as permissões atuais do usuário
        $sql .= "Delete From administracao.permissao
                Where numcgm = ".$this->numCgm."; ";
        //Copia as permissões do usuário selecionado
        $sql .= "Insert Into administracao.permissao (numcgm, cod_acao, ano_exercicio)
                    Select ".$this->numCgm.", cod_acao, ano_exercicio
                    From administracao.permissao
                    Where numcgm = ".$this->numCgmImportado."; ";
        $conn = new databaseLegado;
        $conn->abreBD();
        if ($conn->executaSql($sql)) {
            $ok = true;
        } else {
            $ok = false;
        }
        $conn->fechaBD();

        return $ok;
    }

} // Fim da classe permissao
?>

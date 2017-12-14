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
    * Classe de Regra de Negócio de Relatório de Programa
    * Data de Criação: 06/11/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage

    * Casos de uso: UC-02.09
*/
require_once '../../../../../../gestaoFinanceira/fontes/PHP/ppa/classes/mapeamento/TPPAPrograma.class.php';
require_once '../../../../../../gestaoFinanceira/fontes/PHP/ppa/classes/mapeamento/TPPAProgramaOrgaoResponsavel.class.php';
require_once '../../../../../../gestaoFinanceira/fontes/PHP/ppa/classes/mapeamento/TPPAProgramaResponsavel.class.php';

class RPPARelatorioPrograma
{
    /**
        * Atributo que recebe o array de Mapeamentos
        * @name $pgForm
    */
    private $arMapeamentos;

    /**
        * Atributo que recebe a página responsável
        * @name $pgForm
    */
    private $pgForm;

    /**
        * Método Construtor da Classe
        * @return void
    */
    public function __construct()
    {
        $this->pgForm = "FMRelatorioPrograma.php";
    }

    /**
        * Método que executa o Mapeamento com seu método requerido
        * @param String $stMapeamento
        * @param String $stMetodo
        * @param String $stCriterio
        * @param String $stOrdem
        * @return object
    */
    protected function callMapeamento($stMapeamento, $stMetodo, $stCriterio = "", $stOrdem = "", $boCriterio = false)
    {
        if ($stCriterio && $boCriterio == false) {
            $stCriterio = ' WHERE ' . $stCriterio;
        }

        $obMapeamento = new $stMapeamento();
        $obMapeamento->$stMetodo($obRecordSet, $stCriterio, $stOrdem);

        return $obRecordSet;
    }

    /**
        * Método que executa o Mapeamento com seu método requerido mostra o SQL
        * @param String $stMapeamento
        * @param String $stMetodo
        * @return String
    */
    protected function callMapeamentoSQL($stMapeamento, $stMetodo)
    {
        $obMapeamento = new $stMapeamento();
        $stSql = $obMapeamento->$stMetodo();

        return $stSql;
    }

    /**
        * Método que executa uma consulta SQL
        * @param String $stMap
        * @param String $stMetodo
        * @param String $stParam
        * @param String $stOrdem
        * @return object
    */
    public function pesquisar($stMap, $stMetodo, $stParam, $stOrdem)
    {
        return $this->callMapeamento($stMap, $stMetodo, $stParam, $stOrdem);
    }

    /**
        * Método que retorna o SQL desejado pelo Mapeamento escolhido
        * @param String $stMap
        * @param String $stMetodo
        * @return String
    */
    public function showSQL($stMap, $stMetodo)
    {
        return $this->callMapeamentoSQL($stMap, $stMetodo);
    }
}
?>

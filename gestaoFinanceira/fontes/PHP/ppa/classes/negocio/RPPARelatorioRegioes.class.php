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
    * Classe de Regra de Negócio de Relatório de Regiões
    * Data de Criação: 13/10/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage

    * Casos de uso: UC-02.09.07
*/
require_once '../../../../../../gestaoFinanceira/fontes/PHP/ppa/classes/mapeamento/TPPAPPAEncaminhamento.class.php';
require_once '../../../../../../gestaoFinanceira/fontes/PHP/ppa/classes/mapeamento/TPPAPPANorma.class.php';
require_once '../../../../../../gestaoFinanceira/fontes/PHP/ppa/classes/mapeamento/TPPAPPAPublicacao.class.php';
require_once '../../../../../../gestaoFinanceira/fontes/PHP/ppa/classes/mapeamento/TPPAPeriodicidade.class.php';

class RPPARelatorioRegioes
{
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
        $this->pgForm = "FMRelatorioRegioes.php";
    }

    /**
        * Método que executa o Mapeamento com seu método requerido
        * @param String $stMapeamento
        * @param String $stMetodo
        * @param String $stCriterio
        * @param String $stOrdem
        * @return object
    */
    protected function callMapeamento($stMapeamento, $stMetodo, $stCriterio = "", $stOrdem = "")
    {
        if ($stCriterio) {
            $stCriterio = ' WHERE ' . $stCriterio;
        }

        $obMapeamento = new $stMapeamento();
        $obMapeamento->$stMetodo($obRecordSet, $stCriterio, $stOrdem);

        return $obRecordSet;
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
}
?>

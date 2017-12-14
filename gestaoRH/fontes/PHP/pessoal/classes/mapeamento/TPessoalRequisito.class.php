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
  * Classe de mapeamento da tabela pessoal.requisito
  * Data de Criação: 19/10/2012

  * @author Analista:
  * @author Desenvolvedor: Davi Ritter Aroldi

  * @package URBEM
  * @subpackage Mapeamento

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  pessoal.requisito
  * Data de Criação: 19/10/2012

  * @author Analista:
  * @author Desenvolvedor: Davi Ritter Aroldi

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalRequisito extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TPessoalRequisito()
    {
        parent::Persistente();
        $this->setTabela('pessoal.requisito');

        $this->setCampoCod('cod_requisito');
        $this->setComplementoChave('');

        $this->AddCampo('cod_requisito'     , 'integer',  true,   '' ,  true, false);
        $this->AddCampo('descricao'         , 'varchar',  true, '200', false, false);

    }

    public function recuperaRequisitosCargo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stGroup = " GROUP BY requisito.cod_requisito
                   , requisito.descricao";
        $stSql = $this->montaRecuperaRequisitosCargo().$stFiltro.$stGroup.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRequisitosCargo()
    {
        $stSql = "SELECT requisito.cod_requisito
                       , requisito.descricao
                    FROM pessoal.requisito
              INNER JOIN pessoal.cargo_requisito
                      ON cargo_requisito.cod_requisito = requisito.cod_requisito
                ";

        return $stSql;
    }

    public function recuperaRequisitosDisponiveisCargo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stGroup = " GROUP BY requisito.cod_requisito
                   , requisito.descricao";
        $stSql = $this->montaRecuperaRequisitosDisponiveisCargo().$stFiltro.$stGroup.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRequisitosDisponiveisCargo()
    {
        $stSql = "SELECT requisito.cod_requisito
                       , requisito.descricao
                    FROM pessoal.requisito
               LEFT JOIN pessoal.cargo_requisito
                      ON cargo_requisito.cod_requisito = requisito.cod_requisito
                ";

        return $stSql;
    }
}

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
  * Data de Criação: 08/10/2009

  * @author Analista:      Gelson Wolowski
  * @author Desenvolvedor: Diogo Zarpelon  <diogo.zarpelon@cnm.org.br>

  * @package URBEM
  * @subpackage

  $Id:$

  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TPatrimonioInventario extends Persistente
{
   /**
    * Método Construtor
    * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('patrimonio.inventario');
        $this->setCampoCod('id_inventario');
        $this->setComplementoChave('exercicio');
        $this->AddCampo('exercicio'     , 'varchar' , true  , '' ,true  , false);
        $this->AddCampo('id_inventario' , 'integer' , true  , '' ,true  , false);
        $this->AddCampo('dt_inicio'     , 'date'    , true  , '' ,false , false);
        $this->AddCampo('dt_fim'        , 'date'    , false , '' ,false , false);
        $this->AddCampo('observacao'    , 'varchar' , false , '' ,false , false);
        $this->AddCampo('processado'    , 'boolean' , false , '' ,false , false);
        $this->AddCampo('numcgm'        , 'integer' , false , '' ,false , true);
    }

    public function recuperaInventarios(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaInventarios",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaInventarios()
    {
        $stSql = "
            SELECT  inventario.exercicio
                 ,  inventario.id_inventario
                 ,  TO_CHAR(inventario.dt_inicio , 'dd/mm/yyyy') as data_inicio
                 ,  TO_CHAR(inventario.dt_fim    , 'dd/mm/yyyy') as data_fim
                 ,  inventario.observacao

              FROM  patrimonio.inventario

             WHERE  1=1
            ".($this->getDado('id_inventario')  ? " AND  inventario.id_inventario = ".$this->getDado('id_inventario'):""). "
            ".($this->getDado('processado')     ? " AND  inventario.processado    = ".$this->getDado('processado'):""). "";

        return $stSql;
    }

    public function recuperaCargaItemPatrimonio(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaCargaItemPatrimonio().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaCargaItemPatrimonio()
    {
        $stSql = "

            SELECT  inventario_historico_bem.cod_bem
                 ,  TRIM(bem.descricao) as descricao
                 ,  inventario_historico_bem.cod_situacao
                 ,  (
                        SELECT  nom_situacao
                          FROM  patrimonio.situacao_bem
                         WHERE  situacao_bem.cod_situacao = inventario_historico_bem.cod_situacao
                    ) as nom_situacao

              FROM  patrimonio.inventario_historico_bem

        INNER JOIN  patrimonio.bem
                ON  bem.cod_bem = inventario_historico_bem.cod_bem

             WHERE  1=1

               AND  NOT EXISTS
                    (
                         SELECT  1
                           FROM  patrimonio.bem_baixado
                          WHERE  bem_baixado.cod_bem = bem.cod_bem
                    ) ";

        if ($this->getDado('id_inventario')) {
            $stSql .= " AND  inventario_historico_bem.id_inventario = ".$this->getDado('id_inventario');
        }

        if ($this->getDado('exercicio')) {
            $stSql .= " AND  inventario_historico_bem.exercicio = ".$this->getDado('exercicio');
        }

        if ($this->getDado('cod_orgao')) {
            $stSql .= " AND  inventario_historico_bem.cod_orgao = ".$this->getDado('cod_orgao');
        }

        if ($this->getDado('cod_local')) {
            $stSql .= " AND  inventario_historico_bem.cod_local = ".$this->getDado('cod_local');
        }

        return $stSql;

    }

    public function recuperaCargaInicialInventario(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaCargaInicialInventario().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaCargaInicialInventario()
    {
        $stSql = "";

        # Chamada para a PL que popula a tabela de inventario_historico_bem
        if ($this->getDado('id_inventario') && $this->getDado('exercicio')) {
            $stSql = "SELECT patrimonio.fn_carga_inventario_patrimonio
                             (
                                   '".$this->getDado('exercicio')."'
                                ,  ".$this->getDado('id_inventario')."
                                ,  ".Sessao::read('numCgm')."
                             )";
        }

        return $stSql;
    }

    public function recuperaInventarioProcessamento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaInventarioProcessamento().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaInventarioProcessamento()
    {
        $stSql = "";

        # Chamada para a PL que popula a tabela de inventario_historico_bem
        if ($this->getDado('id_inventario') && $this->getDado('exercicio')) {
            $stSql = "SELECT patrimonio.fn_inventario_processamento
                             (
                                   '".$this->getDado('exercicio')."'
                                ,  ".$this->getDado('id_inventario')."
                             )";
        }

        return $stSql;
    }

    public function recuperaNroTotalBem(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaNroTotalBem().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaNroTotalBem()
    {
        /*$stSql  = "     SELECT  COUNT(historico_bem.cod_bem) as total             \n";
        $stSql .= "                                                               \n";
        $stSql .= "       FROM  patrimonio.historico_bem                          \n";
        $stSql .= "                                                               \n";
        $stSql .= " INNER JOIN  (                                                 \n";
        $stSql .= "                SELECT  cod_bem                                \n";
        $stSql .= "                     ,  MAX(timestamp) AS timestamp            \n";
        $stSql .= "                  FROM  patrimonio.historico_bem               \n";
        $stSql .= "              GROUP BY  cod_bem                                \n";
        $stSql .= "             ) as resumo                                       \n";
        $stSql .= "         ON  resumo.cod_bem   = historico_bem.cod_bem          \n";
        $stSql .= "        AND  resumo.timestamp = historico_bem.timestamp        \n";
        $stSql .= "                                                               \n";
        $stSql .= " INNER JOIN  patrimonio.inventario_historico_bem               \n";
        $stSql .= "         ON  inventario_historico_bem.cod_bem = resumo.cod_bem \n";
        $stSql .= "                                                               \n";
        $stSql .= "  WHERE  1=1                                                   \n";*/

    $stSql = "SELECT
            local.cod_local ,
            cod_logradouro ,
            numero ,
            fone ,
            ramal ,
            dificil_acesso ,
            insalubre ,
            descricao
            FROM
            organograma.local

            INNER JOIN (
                SELECT  historico_bem.cod_local
                  FROM  patrimonio.historico_bem

                INNER JOIN  (
                       SELECT  cod_bem
                        ,  MAX(timestamp) AS timestamp
                         FROM  patrimonio.historico_bem
                     GROUP BY  cod_bem
                    ) as max_historico_bem

                    ON  max_historico_bem.cod_bem   = historico_bem.cod_bem
                   AND  max_historico_bem.timestamp = historico_bem.timestamp

                INNER JOIN  patrimonio.inventario_historico_bem
                    ON  inventario_historico_bem.cod_bem = historico_bem.cod_bem

                 WHERE  1=1";

        if ($this->getDado('id_inventario')) {
            $stSql .= " AND  inventario_historico_bem.id_inventario = ".$this->getDado('id_inventario');
        }

        if ($this->getDado('exercicio')) {
            $stSql .= " AND  inventario_historico_bem.exercicio = ".$this->getDado('exercicio');
        }

        if ($this->getDado('cod_orgao')) {
            $stSql .= " AND  historico_bem.cod_orgao = ".$this->getDado('cod_orgao');
        }

        if ($this->getDado('cod_local')) {
            $stSql .= " AND  historico_bem.cod_local = ".$this->getDado('cod_local');
        }

    $stSql.= ") AS historico_bem
               ON  historico_bem.cod_local                = local.cod_local
             GROUP BY local.cod_local ,
                  cod_logradouro ,
                  numero ,
                  fone ,
                  ramal ,
                  dificil_acesso ,
                  insalubre ,
                  descricao ";

        return $stSql;
    }

    public function recuperaNovoPrimeiroInventario(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaNovoPrimeiroInventario().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }


}

?>

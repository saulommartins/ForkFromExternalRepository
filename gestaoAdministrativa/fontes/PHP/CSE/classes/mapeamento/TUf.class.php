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
* Classe de Mapeamento para tabela de estados
* Data de Criação: 22/06/2007

$Id: TUf.class.php 59612 2014-09-02 12:00:51Z gelson $

* @author Analista     : Fábio Bertoldi
* @author Desenvolvedor: Rodrigo

Casos de uso: uc-01.07.15
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GA_CSE_MAPEAMENTO."TPais.class.php"                                             );

class TUf extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    public function TUf()
    {
        parent::Persistente();
        $this->setTabela( "sw_uf" );

        $this->setCampoCod('cod_uf');
        $this->setComplementoChave('cod_uf,cod_pais');

        $this->AddCampo('cod_uf','sequence',true,'',true,true );
        $this->AddCampo('cod_pais','integer',true,'',true,true);
        $this->AddCampo('nom_uf','varchar',true,'50',true,true);
        $this->AddCampo('sigla_uf','char',true,'2',true,true  );
    }

    public function recuperaEstado(&$rsRecordSet,$stFiltro="",$stOrdem="",$boTransacao="")
    {
        $obErro        = new Erro;
        $obConexao     = new Conexao;
        $rsRecordSet   = new RecordSet;
        $stSql         = $this->montaRecuperaEstado().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro        = $obConexao->executaSQL($rsRecordSet,$stSql,$boTransacao);

        return $obErro;
    }

    public function montaRecuperaEstado()
    {
          $stSql = " SELECT sw_uf.cod_uf                            \n";
          $stSql.= "       ,sw_uf.cod_pais                          \n";
          $stSql.= "       ,sw_uf.nom_uf                            \n";
          $stSql.= "       ,sw_uf.sigla_uf                          \n";
          $stSql.= "       ,sw_pais.nom_pais                        \n";
          $stSql.= "   FROM sw_uf                                   \n";
          $stSql.= "       ,sw_pais                                 \n";
          $stSql.= "  WHERE sw_uf.cod_pais = sw_pais.cod_pais       \n";

          return $stSql;
    }

    public function mostraTodosEstadoCgm(&$rsRecordSet,$stFiltro="",$stOrdem="",$boTransacao="")
    {
        $obErro        = new Erro;
        $obConexao     = new Conexao;
        $rsRecordSet   = new RecordSet;
        if ($stOrdem == '') {
            $stOrdem   = " ORDER BY sw_uf.nom_uf ASC";
        }
        $stGroupBy     = " GROUP BY sw_uf.cod_uf, sw_uf.nom_uf ";
        $stSql         = $this->montaListaEstadoCgm().$stFiltro.$stGroupBy.$stOrdem;
        $this->stDebug = $stSql;
        $obErro        = $obConexao->executaSQL($rsRecordSet,$stSql,$boTransacao);

        return $obErro;
    }

    public function montaListaEstadoCgm()
    {
        $stSql  = "SELECT sw_uf.cod_uf AS cod_uf	 \n";
        $stSql .= "     , sw_uf.nom_uf AS nom_uf	 \n";
        $stSql .= "  FROM sw_uf 			 \n";
        $stSql .= " INNER JOIN sw_cgm 			 \n";
        $stSql .= "    ON sw_uf.cod_uf=sw_cgm.cod_uf 	 \n";
        $stSql .= "   AND sw_uf.cod_pais=sw_cgm.cod_pais \n";
        $stSql .= " WHERE sw_uf.cod_uf > 0 		 \n";

        return $stSql;
    }
}

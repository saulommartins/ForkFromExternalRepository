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
* Classe de Mapeamento para tabela de municipios
* Data de Criação: 25/06/2007

* @author Analista     : Fábio Bertoldi
* @author Desenvolvedor: Rodrigo

$Id: TMunicipio.class.php 59612 2014-09-02 12:00:51Z gelson $

Casos de uso: uc-01.07.16
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TMunicipio extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    public function TMunicipio()
    {
        parent::Persistente();
        $this->setTabela( "sw_municipio" );

        $this->setCampoCod('cod_municipio','cod_uf');
        $this->setComplementoChave('cod_uf');

        $this->AddCampo('cod_municipio','sequence',true,'',true,true );
        $this->AddCampo('cod_uf','integer',true,'',true,true         );
        $this->AddCampo('nom_municipio','varchar',true,'35',true,true);
    }

    public function recuperaMunicipio(&$rsRecordSet,$stFiltro="",$stOrdem="",$boTransacao="")
    {
        $obErro        = new Erro;
        $obConexao     = new Conexao;
        $rsRecordSet   = new RecordSet;
        $stOrdem       = " ORDER BY sw_municipio.nom_municipio ASC ";
        $stSql         = $this->montaRecuperaMunicipio().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro        = $obConexao->executaSQL($rsRecordSet,$stSql,$boTransacao);

        return $obErro;
    }

    public function montaRecuperaMunicipio()
    {
          $stSql = " SELECT sw_municipio.cod_municipio              \n";
          $stSql.= "       ,sw_municipio.nom_municipio              \n";
          $stSql.= "       ,sw_uf.cod_uf                            \n";
          $stSql.= "       ,sw_uf.nom_uf                            \n";
          $stSql.= "       ,sw_pais.cod_pais                        \n";
          $stSql.= "       ,sw_pais.nom_pais                        \n";
          $stSql.= "   FROM sw_uf                                   \n";
          $stSql.= "       ,sw_pais                                 \n";
          $stSql.= "       ,sw_municipio                            \n";
          $stSql.= "  WHERE sw_uf.cod_pais = sw_pais.cod_pais       \n";
          $stSql.= "    AND sw_uf.cod_uf   = sw_municipio.cod_uf    \n";

          return $stSql;
    }

    public function mostraTodosMunicipiosCgm(&$rsRecordSet,$stFiltro="",$stOrdem="",$boTransacao="")
    {
        $obErro        = new Erro;
        $obConexao     = new Conexao;
        $rsRecordSet   = new RecordSet;
        if ($stOrdem == '') {
            $stOrdem       = " ORDER BY sw_municipio.nom_municipio ASC ";
        }
        $stGroupBy     = " GROUP BY sw_municipio.cod_municipio, sw_municipio.nom_municipio ";
        $stSql         = $this->montaListaMunicipiosCgm().$stFiltro.$stGroupBy.$stOrdem;
        $this->stDebug = $stSql;
        $obErro        = $obConexao->executaSQL($rsRecordSet,$stSql,$boTransacao);

        return $obErro;
    }

    public function montaListaMunicipiosCgm()
    {
        $stSql  = "SELECT sw_municipio.cod_municipio AS cod_municipio	  \n";
        $stSql .= "     , sw_municipio.nom_municipio AS nom_municipio	  \n";
        $stSql .= "  FROM sw_municipio 					  \n";
        $stSql .= " INNER JOIN sw_cgm 					  \n";
        $stSql .= "    ON sw_municipio.cod_municipio=sw_cgm.cod_municipio \n";
        $stSql .= "   AND sw_municipio.cod_uf=sw_cgm.cod_uf               \n";
        $stSql .= " WHERE sw_municipio.cod_municipio > 0 		  \n";

        return $stSql;
    }
}

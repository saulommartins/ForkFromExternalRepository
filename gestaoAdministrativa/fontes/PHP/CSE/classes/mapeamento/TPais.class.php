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
* Classe de Mapeamento para tabela de paises
* Data de Criação: 18/06/2007

* @author Analista     : Fábio Bertoldi
* @author Desenvolvedor: Rodrigo

$Id: TPais.class.php 59612 2014-09-02 12:00:51Z gelson $

Casos de uso: uc-01.07.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TPais extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    public function TPais()
    {
        parent::Persistente();
        $this->setTabela( "sw_pais" );

        $this->setCampoCod('cod_pais');
        $this->setComplementoChave('cod_rais');

        $this->AddCampo('cod_pais','sequence',true,'',true,true      );
        $this->AddCampo('cod_rais','integer',true,'',true,true       );
        $this->AddCampo('nom_pais','varchar',true,'20',true,true     );
        $this->AddCampo('nacionalidade','varchar',true,'80',true,true);
    }

    public function mostraTodosPaisesCgm(&$rsRecordSet,$stFiltro="",$stOrdem="",$boTransacao="")
    {
        $obErro        = new Erro;
        $obConexao     = new Conexao;
        $rsRecordSet   = new RecordSet;
        $stSql         = $this->montaListaPaisesCgm().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro        = $obConexao->executaSQL($rsRecordSet,$stSql,$boTransacao);

        return $obErro;
    }

    public function montaListaPaisesCgm()
    {
        $stSql  = "SELECT sw_pais.cod_pais AS cod_pais		\n";
        $stSql .= "     , sw_pais.nom_pais AS nom_pais		\n";
        $stSql .= "  FROM sw_pais INNER JOIN sw_cgm 		\n";
        $stSql .= "    ON sw_pais.cod_pais=sw_cgm.cod_pais	\n";
        $stSql .= " WHERE sw_pais.cod_pais > 0			\n";
        $stSql .= " GROUP BY sw_pais.cod_pais, sw_pais.nom_pais  \n";
        $stSql .= " ORDER BY sw_pais.nom_pais			\n";

        return $stSql;
    }
}

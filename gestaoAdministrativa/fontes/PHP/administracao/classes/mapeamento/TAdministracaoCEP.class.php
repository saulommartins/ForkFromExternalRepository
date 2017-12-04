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
* Classe de mapeamento para administracao.cep
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3476 $
$Name$
$Author: pablo $
$Date: 2005-12-06 13:51:37 -0200 (Ter, 06 Dez 2005) $

$Id: TAdministracaoCEP.class.php 63632 2015-09-22 17:42:03Z michel $

Casos de uso: uc-01.03.98
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
class TCEP extends Persistente
{
    function __construct()
    {
        parent::Persistente();
        $this->setTabela('sw_cep');
        $this->setCampoCod('cep');
    
        $this->AddCampo('cep',          'varchar', true,  8, true,  false);
        $this->AddCampo('cep_anterior', 'varchar', true,  8, true,  false);
    }
    
    function recuperaCepBairro(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stOrdem = ( $stOrdem != "" ) ? " ORDER BY ".$stOrdem : " ORDER BY sw_uf.nom_uf, sw_municipio.nom_municipio, sw_bairro.nom_bairro";
        $stSql  = $this->montaCepBairro().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    function montaCepBairro()
    {
        $stSql = "     SELECT sw_cep.cep
                            , sw_bairro_logradouro.cod_bairro
                            , sw_bairro_logradouro.cod_municipio
                            , sw_bairro_logradouro.cod_uf
                            , sw_uf.nom_uf
                            , sw_municipio.nom_municipio
                            , sw_bairro.nom_bairro
                         FROM sw_cep
                   INNER JOIN sw_cep_logradouro
                           ON sw_cep_logradouro.cep = sw_cep.cep
                   INNER JOIN sw_bairro_logradouro
                           ON sw_bairro_logradouro.cod_logradouro = sw_cep_logradouro.cod_logradouro
                   INNER JOIN sw_bairro
                           ON sw_bairro.cod_uf = sw_bairro_logradouro.cod_uf
                          AND sw_bairro.cod_municipio = sw_bairro_logradouro.cod_municipio
                          AND sw_bairro.cod_bairro = sw_bairro_logradouro.cod_bairro
                   INNER JOIN sw_uf
                           ON sw_uf.cod_uf = sw_bairro.cod_uf
                   INNER JOIN sw_municipio
                           ON sw_municipio.cod_municipio = sw_bairro.cod_municipio
                          AND sw_municipio.cod_uf = sw_bairro.cod_uf
        ";

        if($this->getDado('cep'))
            $stSql .= " WHERE sw_cep.cep='".$this->getDado('cep')."' ";

        $stSql .= "  GROUP BY sw_cep.cep
                            , sw_bairro_logradouro.cod_bairro
                            , sw_bairro_logradouro.cod_municipio
                            , sw_bairro_logradouro.cod_uf
                            , sw_bairro.nom_bairro
                            , sw_uf.nom_uf
                            , sw_municipio.nom_municipio
        ";
    
        return $stSql;
    }
}

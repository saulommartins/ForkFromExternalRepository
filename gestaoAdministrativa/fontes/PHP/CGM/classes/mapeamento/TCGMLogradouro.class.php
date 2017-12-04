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
* Classe de mapeamento da tabela grau_parentesco
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 18800 $
$Name$
$Author: souzadl $
$Date: 2006-12-15 14:17:30 -0200 (Sex, 15 Dez 2006) $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TCGMLogradouro extends Persistente
{
    public function TCGMLogradouro()
    {
        parent::Persistente();
        $this->setTabela('sw_cgm_logradouro');
        $this->setCampoCod('cod_logradouro');

        $this->AddCampo('numcgm',           'integer', true, '', true,  true);
        $this->AddCampo('cod_logradouro',   'integer', true, '', true,  true);
        $this->AddCampo('cod_bairro',       'integer', true, '', true,  true);
        $this->AddCampo('cod_municipio',    'integer', true, '', true,  true);
        $this->AddCampo('cod_uf',           'integer', true, '', true,  true);
        $this->AddCampo('cep',              'varchar', true,  8, false, false);
    }


    function recuperaBairroCgm(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaBairroCgm().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    function montaRecuperaBairroCgm()
    {
        $stSql = "  SELECT 
                            numcgm ,
                            cod_logradouro ,
                            cod_bairro ,
                            cod_municipio ,
                            cod_uf ,
                            cep 
                    FROM sw_cgm_logradouro
                    WHERE cod_bairro    = ".$this->getDado('cod_bairro')." 
                      AND cod_uf        = ".$this->getDado('cod_uf')."
                      AND cod_municipio = ".$this->getDado('cod_municipio')."
                ";

        return $stSql;
    }

    function recuperaLogradouroCgm(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaLogradouroCgm().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    function montaRecuperaLogradouroCgm()
    {
        $stSql = "  SELECT 
                            numcgm ,
                            cod_logradouro ,
                            cod_bairro ,
                            cod_municipio ,
                            cod_uf ,
                            cep 
                    FROM sw_cgm_logradouro
                    WHERE cod_logradouro = ".$this->getDado('cod_logradouro')." 
                      AND cod_uf         = ".$this->getDado('cod_uf')."
                      AND cod_municipio  = ".$this->getDado('cod_municipio')."
                ";

        return $stSql;
    }

}

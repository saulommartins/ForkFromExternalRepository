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
* Classe de Mapeamento para tabela cgm
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Id: TCGM.class.php 63681 2015-09-29 17:48:28Z evandro $

$Revision: 28411 $
$Name$
$Author: diogo.zarpelon $
$Date: 2008-03-06 16:32:26 -0300 (Qui, 06 Mar 2008) $

Casos de uso: uc-01.02.92, uc-01.02.93
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TCGM extends Persistente
{
function TCGM()
{
    parent::Persistente();
    $this->setTabela('sw_cgm');
    $this->setCampoCod('numcgm');

    $this->AddCampo('numcgm',                  'integer',  true,  '', true,  false);
    $this->AddCampo('cod_municipio',           'integer',  true,  '', false, false);
    $this->AddCampo('cod_uf',                  'integer',  true,  '', false, false);
    $this->AddCampo('cod_pais',                'integer',  true,  '', false, false);
    $this->AddCampo('cod_pais_corresp',        'integer',  true,  '', false, false);
    $this->AddCampo('cod_municipio_corresp',   'integer',  true,  '', false, false);
    $this->AddCampo('cod_responsavel',         'integer',  true,  '', false, false);
    $this->AddCampo('cod_uf_corresp',          'integer',  true,  '', false, false);
    $this->AddCampo('nom_cgm',                 'varchar',  true, 200, false, false);
    $this->AddCampo('tipo_logradouro',         'varchar',  true,  10, false, false);
    $this->AddCampo('logradouro',              'varchar',  true,  60, false, false);
    $this->AddCampo('numero',                  'varchar',  true,   6, false, false);
    $this->AddCampo('complemento',             'varchar',  true,  20, false, false);
    $this->AddCampo('bairro',                  'varchar',  true,  30, false, false);
    $this->AddCampo('cep',                     'varchar',  true,   8, false, false);
    $this->AddCampo('tipo_logradouro_corresp', 'varchar',  true,  10, false, false);
    $this->AddCampo('logradouro_corresp',      'varchar',  true,  60, false, false);
    $this->AddCampo('numero_corresp',          'varchar',  true,   6, false, false);
    $this->AddCampo('complemento_corresp',     'varchar',  true,  20, false, false);
    $this->AddCampo('bairro_corresp',          'varchar',  true,  30, false, false);
    $this->AddCampo('cep_corresp',             'varchar',  true,   8, false, false);
    $this->AddCampo('fone_residencial',        'char',     true,  12, false, false);
    $this->AddCampo('ramal_residencial',       'char',     true,   6, false, false);
    $this->AddCampo('fone_comercial',          'char',     true,  12, false, false);
    $this->AddCampo('ramal_comercial',         'char',     true,   6, false, false);
    $this->AddCampo('fone_celular',            'char',     true,  12, false, false);
    $this->AddCampo('e_mail',                  'char',     true, 100, false, false);
    $this->AddCampo('e_mail_adcional',         'char',     true, 100, false, false);
    $this->AddCampo('dt_cadastro',             'date',     false, '', false, false);
}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT                                                             \n";
    $stSql .= "    CGM.numcgm as numcgm2,                                         \n";
    $stSql .= "    CGM.*,                                                         \n";
    $stSql .= "    PF.*,                                                          \n";
    $stSql .= "    PJ.*                                                           \n";
    $stSql .= "FROM                                                               \n";
    $stSql .= "    (                                                              \n";
    $stSql .= "        SELECT                                                     \n";
    $stSql .= "            CGM.*,                                                 \n";
    $stSql .= "            MU.nom_municipio,                                      \n";
    $stSql .= "            UF.nom_uf,                                             \n";
    $stSql .= "            CGM.tipo_logradouro||' '||CGM.logradouro||', '||CGM.numero   \n";
    $stSql .= "            ||' '||CGM.complemento AS endereco,                          \n";
    $stSql .= "            MU.nom_municipio||' - '||UF.nom_uf as municipio_uf           \n";
    $stSql .= "        FROM                                                       \n";
    $stSql .= "            sw_cgm       AS CGM,                                  \n";
    $stSql .= "            sw_municipio AS MU,                                   \n";
    $stSql .= "            sw_uf        AS UF                                    \n";
    $stSql .= "        WHERE                                                      \n";
    $stSql .= "            CGM.cod_municipio = MU.cod_municipio AND               \n";
    $stSql .= "            CGM.cod_uf = MU.cod_uf               AND               \n";
    $stSql .= "            MU.cod_uf = UF.cod_uf                AND               \n";
    $stSql .= "            CGM.numcgm <> 0                                        \n";
    $stSql .= "    ) as CGM                                                       \n";
    $stSql .= "LEFT JOIN                                                          \n";
    $stSql .= "    sw_cgm_pessoa_fisica AS PF                                    \n";
    $stSql .= "ON                                                                 \n";
    $stSql .= "    CGM.numcgm = PF.numcgm                                         \n";
    $stSql .= "LEFT JOIN                                                          \n";
    $stSql .= "    sw_cgm_pessoa_juridica AS PJ                                  \n";
    $stSql .= "ON                                                                 \n";
    $stSql .= "    CGM.numcgm = PJ.numcgm                                         \n";

    return $stSql;
}

/**
    * mesma função do recuperatodos, mas efetua chamada a outro método para montar o sql, o método montarecuperarelacionamentosintetico.
    * @access public
    * @param  object  $rsrecordset objeto recordset
    * @param  string  $stcondicao  string de condição do sql (where)
    * @param  string  $stordem     string de ordenação do sql (order by)
    * @param  boolean $botransacao
    * @return object  objeto erro
*/
function recuperarelacionamentosintetico(&$rsrecordset, $stcondicao = "" , $stordem = "" , $botransacao = "")
{
    $oberro      = new erro;
    $obconexao   = new conexao;
    $rsrecordset = new recordset;

    if (trim($stordem)) {
        $stordem = strtolower( $stordem );
        $stordem = (strpos($stordem,"order by")===false)?" order by $stordem":$stordem;
    }
    $stsql = $this->montarecuperarelacionamentosintetico().$stcondicao.$stordem;
    $this->setdebug( $stsql );
    $oberro = $obconexao->executasql( $rsrecordset, $stsql, $botransacao );

    return $oberro;
}

function montaRecuperaRelacionamentoSintetico()
{
    $stSql  = " SELECT                           \n";
    $stSql .= "     CGM.numcgm,                  \n";
    $stSql .= "     CGM.nom_cgm,                 \n";
    $stSql .= "     PF.cpf,                      \n";
    $stSql .= "     PJ.cnpj,                     \n";
    $stSql .= "     CASE WHEN PF.cpf IS NOT NULL THEN PF.cpf ELSE PJ.cnpj END AS documento \n";
    $stSql .= " FROM                             \n";
    $stSql .= "     SW_CGM AS CGM                \n";
    $stSql .= " LEFT JOIN                        \n";
    $stSql .= "     sw_cgm_pessoa_fisica AS PF   \n";
    $stSql .= " ON                               \n";
    $stSql .= "     CGM.numcgm = PF.numcgm       \n";
    $stSql .= " LEFT JOIN                        \n";
    $stSql .= "     sw_cgm_pessoa_juridica AS PJ \n";
    $stSql .= " ON                               \n";
    $stSql .= "     CGM.numcgm = PJ.numcgm       \n";
    $stSql .= " WHERE                            \n";
    $stSql .= "     CGM.numcgm <> 0              \n";

    return $stSql;
}

function recuperaRelacionamentoFornecedor(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new erro;
    $obConexao   = new conexao;
    $rsRecordset = new recordset;

    $stSql = $this->montaRecuperaRelacionamentoFornecedor().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executasql( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoFornecedor()
{
    $stSql  = " SELECT                                      \n\r";
    $stSql .= "     CGM.numcgm,                             \n\r";
    $stSql .= "     CGM.nom_cgm,                            \n\r";
    $stSql .= "     CGM.tipo_logradouro,                    \n\r";
    $stSql .= "     CGM.logradouro,                         \n\r";
    $stSql .= "     CGM.numero,                             \n\r";
    $stSql .= "     CGM.complemento,                        \n\r";
    $stSql .= "     CGM.bairro,                             \n\r";
    $stSql .= "     PF.cpf,                                 \n\r";
    $stSql .= "     sw_uf.sigla_uf AS uf,                   \n\r";
    $stSql .= "     sw_municipio.nom_municipio AS cidade,   \n\r";
    $stSql .= "     PJ.cnpj,                                \n\r";
    $stSql .= "     CASE WHEN PF.cpf IS NOT NULL THEN PF.cpf ELSE PJ.cnpj END AS documento,  \n\r";
    $stSql .= "     CASE WHEN PF.cpf IS NOT NULL THEN 'Física' ELSE 'Jurídica' END AS pessoa \n\r";
    $stSql .= " FROM                                \n\r";
    $stSql .= "     sw_municipio,                   \n\r";
    $stSql .= "     sw_uf,                          \n\r";
    $stSql .= "     SW_CGM AS CGM                   \n\r";
    $stSql .= " LEFT JOIN                           \n\r";
    $stSql .= "     sw_cgm_pessoa_fisica AS PF      \n\r";
    $stSql .= " ON                                  \n\r";
    $stSql .= "     CGM.numcgm = PF.numcgm          \n\r";
    $stSql .= " LEFT JOIN                           \n\r";
    $stSql .= "     sw_cgm_pessoa_juridica AS PJ    \n\r";
    $stSql .= " ON                                  \n\r";
    $stSql .= "     CGM.numcgm = PJ.numcgm          \n\r";
    $stSql .= " WHERE                               \n\r";
    $stSql .= "     sw_municipio.cod_municipio = CGM.cod_municipio  \n\r";
    $stSql .= "     AND sw_municipio.cod_uf = sw_uf.cod_uf          \n\r";
    $stSql .= "     AND sw_uf.cod_uf = CGM.cod_uf                   \n\r";
    $stSql .= "     AND CGM.numcgm = ".$this->getDado('numcgm')."   \n\r";

    return $stSql;
}

/**
    * mesma função do recuperatodos, mas efetua chamada a outro método para montar o sql, o método montarecuperarelacionamentosintetico.
    * @access public
    * @param  object  $rsrecordset objeto recordset
    * @param  string  $stcondicao  string de condição do sql (where)
    * @param  string  $stordem     string de ordenação do sql (order by)
    * @param  boolean $botransacao
    * @return object  objeto erro
*/
function recuperarelacionamentosinteticoComUsuario(&$rsrecordset, $stcondicao = "" , $stordem = "" , $botransacao = "")
{
    $oberro      = new erro;
    $obconexao   = new conexao;
    $rsrecordset = new recordset;

    if (trim($stordem)) {
        $stordem = strtolower( $stordem );
        $stordem = (strpos( $stordem,"order by") ===false)?" order by $stordem":$stordem;
    }
    $stsql = $this->montarecuperarelacionamentosinteticoComUsuario().$stcondicao.$stordem;
    $this->setdebug( $stsql );
    $oberro = $obconexao->executasql( $rsrecordset, $stsql, $botransacao );

    return $oberro;
}

function montaRecuperaRelacionamentoSinteticoComUsuario()
{
    $stSql  = " SELECT                           \n";
    $stSql .= "     CGM.numcgm,                  \n";
    $stSql .= "     CGM.nom_cgm,                 \n";
    $stSql .= "     PF.cpf,                      \n";
    $stSql .= "     PJ.cnpj,                     \n";
    $stSql .= "     CASE WHEN PF.cpf IS NOT NULL THEN PF.cpf ELSE PJ.cnpj END AS documento, \n";
    $stSql .= "     U.cod_orgao,                 \n";
    $stSql .= "     U.dt_cadastro,               \n";
    $stSql .= "     U.username,                  \n";
    $stSql .= "     U.password,                  \n";
    $stSql .= "     U.status                     \n";
    $stSql .= " FROM                             \n";
    $stSql .= "     administracao.usuario AS U,  \n";
    $stSql .= "     SW_CGM AS CGM                \n";
    $stSql .= " LEFT JOIN                        \n";
    $stSql .= "     sw_cgm_pessoa_fisica AS PF   \n";
    $stSql .= " ON                               \n";
    $stSql .= "     CGM.numcgm = PF.numcgm       \n";
    $stSql .= " LEFT JOIN                        \n";
    $stSql .= "     sw_cgm_pessoa_juridica AS PJ \n";
    $stSql .= " ON                               \n";
    $stSql .= "     CGM.numcgm = PJ.numcgm       \n";
    $stSql .= " WHERE                            \n";
    $stSql .= "     CGM.numcgm <> 0  AND         \n";
    $stSql .= "     CGM.numcgm = U.numcgm        \n";

    return $stSql;
}

/*  busca cgm vinculados a alguma popup, ex: fornecedor, fara um inner join para pegar somente cgm que estejam na tabela
    patrimonio.fornecedor .
*/
function recuperaRelacionamentoVinculado(&$rsrecordset, $stcondicao = "" , $stordem = "" , $botransacao = "" , $tabelaVinculo = "" , $campoVinculo = "", $filtroVinculo="")
{
    $oberro      = new erro;
    $obconexao   = new conexao;
    $rsrecordset = new recordset;

    if ( trim( $stordem ) ) {
        $stordem = strtolower( $stordem );
        $stordem = ( strpos( $stordem , "order by" ) === false ) ? " order by $stordem" : $stordem;
    }

    $stsql = $this->montaRecuperaRelacionamentoVinculado( $tabelaVinculo  , $campoVinculo, $filtroVinculo) . $stcondicao . $stordem;
    $this->setdebug( $stsql );
    $oberro = $obconexao->executasql( $rsrecordset, $stsql, $botransacao );

    return $oberro;
}

function montaRecuperaRelacionamentoVinculado($stTabelaVinculo = null  , $stCampoVinculo = null, $filtroVinculo = null)
{
    $stQuebra = "\r\n";
    $stSql  = " SELECT                           ".$stQuebra;
    $stSql .= "     CGM.numcgm                  ".$stQuebra;
    $stSql .= "     ,CGM.nom_cgm                 ".$stQuebra;

    if (!$stTabelaVinculo) {
        $stSql .= "     ,PF.cpf                      ".$stQuebra;
        $stSql .= "     ,PJ.cnpj                     ".$stQuebra;
        $stSql .= "     CASE WHEN PF.cpf IS NOT NULL THEN PF.cpf ELSE PJ.cnpj END AS documento ".$stQuebra;
    }
    $stSql .= " FROM                             ".$stQuebra;
    $stSql .= "     SW_CGM AS CGM                ".$stQuebra;
    if ($stTabelaVinculo) {
        $stSql .= " LEFT JOIN                        ".$stQuebra;
        $stSql .= "     sw_cgm_pessoa_fisica AS PF   ".$stQuebra;
        $stSql .= " ON                               ".$stQuebra;
        $stSql .= "     CGM.numcgm = PF.numcgm       ".$stQuebra;
        $stSql .= " LEFT JOIN                        ".$stQuebra;
        $stSql .= "     sw_cgm_pessoa_juridica AS PJ ".$stQuebra;
        $stSql .= " ON                               ".$stQuebra;
        $stSql .= "     CGM.numcgm = PJ.numcgm       ".$stQuebra;
    }
    $stSql .= " WHERE                            ".$stQuebra;
    $stSql .= "     CGM.numcgm <> 0              ".$stQuebra;
    if ($stTabelaVinculo) {
        $stSql .= " and exists ( select 1 from  $stTabelaVinculo  as tabela_vinculo
                                 where tabela_vinculo.$stCampoVinculo = CGM.numcgm ".$filtroVinculo.") ";
    }

    return $stSql;
}


/*  busca cgm vinculados a alguma popup, ex: fornecedor, fara um inner join para pegar somente cgm que estejam na tabela
    patrimonio.fornecedor .
*/
function recuperaRelacionamentoVinculadoPlanoSaude(&$rsrecordset, $stcondicao = "" , $stordem = "" , $botransacao = "" , $tabelaVinculo = "" , $campoVinculo = "", $filtroVinculo="")
{
    $oberro      = new erro;
    $obconexao   = new conexao;
    $rsrecordset = new recordset;

    if ( trim( $stordem ) ) {
        $stordem = strtolower( $stordem );
        $stordem = ( strpos( $stordem , "order by" ) === false ) ? " order by $stordem" : $stordem;
    }

    $stsql = $this->montaRecuperaRelacionamentoVinculadoPlanoSaude( $tabelaVinculo  , $campoVinculo, $filtroVinculo) . $stcondicao . $stordem;
    $this->setdebug( $stsql );
    $oberro = $obconexao->executasql( $rsrecordset, $stsql, $botransacao );

    return $oberro;
}

function montaRecuperaRelacionamentoVinculadoPlanoSaude($stTabelaVinculo = null  , $stCampoVinculo = null, $filtroVinculo = null)
{
    
    $stSql  = " SELECT
                        CGM.numcgm
                        ,CGM.nom_cgm
                FROM
                    SW_CGM AS CGM

                LEFT JOIN sw_cgm_pessoa_fisica AS PF
                    ON CGM.numcgm = PF.numcgm
                LEFT JOIN sw_cgm_pessoa_juridica AS PJ
                    ON CGM.numcgm = PJ.numcgm

                JOIN beneficio.layout_fornecedor
                    ON layout_fornecedor.cgm_fornecedor = CGM.numcgm

                JOIN compras.fornecedor
                    ON fornecedor.cgm_fornecedor = layout_fornecedor.cgm_fornecedor

                WHERE CGM.numcgm <> 0
                AND exists (select 1 
                            from ". $stTabelaVinculo ." as tabela_vinculo
                            where tabela_vinculo.$stCampoVinculo = CGM.numcgm ".$filtroVinculo.") 
        ";
    return $stSql;
}

function recuperaRelacionamentoVinculadoComissaoLicitacao(&$rsrecordset, $stcondicao = "" , $stordem = "" , $botransacao = "" , $tabelaVinculo = "" , $campoVinculo = "", $filtroVinculo="")
{
    $oberro      = new erro;
    $obconexao   = new conexao;
    $rsrecordset = new recordset;

    if ( trim( $stordem ) ) {
        $stordem = strtolower( $stordem );
        $stordem = ( strpos( $stordem , "order by" ) === false ) ? " order by $stordem" : $stordem;
    }

    $stsql = $this->montaRecuperaRelacionamentoVinculadoComissaoLicitacao( $tabelaVinculo  , $campoVinculo, $filtroVinculo) . $stcondicao . $stordem;
    $this->setdebug( $stsql );
    $oberro = $obconexao->executasql( $rsrecordset, $stsql, $botransacao );

    return $oberro;
}

function montaRecuperaRelacionamentoVinculadoComissaoLicitacao($stTabelaVinculo = null  , $stCampoVinculo = null, $filtroVinculo = null)
{
    $stSql  = " SELECT
                        SW_CGM.numcgm
                        ,SW_CGM.nom_cgm
                
                FROM licitacao.licitacao
                    
                JOIN licitacao.comissao_licitacao
                    ON comissao_licitacao.cod_licitacao     = licitacao.cod_licitacao
                    AND comissao_licitacao.cod_modalidade   = licitacao.cod_modalidade
                    AND comissao_licitacao.cod_entidade     = licitacao.cod_entidade
                    AND comissao_licitacao.exercicio        = licitacao.exercicio    

                JOIN licitacao.comissao_membros
                    ON comissao_membros.cod_comissao = comissao_licitacao.cod_comissao
                    
                JOIN SW_CGM 
                    ON SW_CGM.numcgm = comissao_membros.numcgm

                LEFT JOIN sw_cgm_pessoa_fisica AS PF
                    ON SW_CGM.numcgm = PF.numcgm

                LEFT JOIN sw_cgm_pessoa_juridica AS PJ
                    ON SW_CGM.numcgm = PJ.numcgm
                
                WHERE SW_CGM.numcgm <> 0
                ".$filtroVinculo."
                
        ";
    return $stSql;

}

function recuperaOrgaoGerenciador(&$rsrecordset, $stcondicao = "" , $stordem = "" , $botransacao = "")
{
    $oberro      = new erro;
    $obconexao   = new conexao;
    $rsrecordset = new recordset;

    if (trim($stordem)) {
        $stordem = strtolower( $stordem );
        $stordem = (strpos( $stordem,"order by") ===false)?" order by $stordem":$stordem;
    }
    $stsql = $this->montaRecuperaOrgaoGerenciador().$stcondicao.$stordem;
    $this->setdebug( $stsql );
    $oberro = $obconexao->executasql( $rsrecordset, $stsql, $botransacao );

    return $oberro;
}

function montaRecuperaOrgaoGerenciador()
{
    $stSql  = " SELECT                                                             
                        CGM.numcgm as numcgm,                                         
                        CGM.nom_cgm
                FROM                                                               
                    (                                                              
                        SELECT                                                     
                            CGM.*,                                                 
                            MU.nom_municipio,                                      
                            UF.nom_uf,                                             
                            CGM.tipo_logradouro||' '||CGM.logradouro||', '||CGM.numero   
                            ||' '||CGM.complemento AS endereco,                          
                            MU.nom_municipio||' - '||UF.nom_uf as municipio_uf           
                        FROM                                                       
                            sw_cgm       AS CGM,                                  
                            sw_municipio AS MU,                                   
                            sw_uf        AS UF                                    
                        WHERE                                                      
                            CGM.cod_municipio = MU.cod_municipio AND               
                            CGM.cod_uf = MU.cod_uf               AND               
                            MU.cod_uf = UF.cod_uf                AND               
                            CGM.numcgm <> 0                                        
                    ) as CGM                                                       
        INNER JOIN sw_cgm_pessoa_juridica AS PJ                                  
                ON CGM.numcgm = PJ.numcgm                                         
    ";
    return $stSql;
}





} // classe

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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * @author Analista: Carlos Adriano
  * @author Desenvolvedor: Carlos Adriano

*/
class TBeneficioBeneficiario extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TBeneficioBeneficiario()
{
    parent::Persistente();
    $this->setTabela("beneficio.beneficiario");

    $this->setCampoCod('cod_contrato');
    $this->setComplementoChave('cod_contrato, cgm_fornecedor, cod_modalidade, cod_tipo_convenio, codigo_usuario, timestamp');

    $this->AddCampo('cod_contrato'             , 'integer'   , true,  ''      , true , true);
    $this->AddCampo('cgm_fornecedor'           , 'integer'   , true,  ''      , true , true);
    $this->AddCampo('cod_modalidade'           , 'integer'   , true,  ''      , true , true);
    $this->AddCampo('cod_tipo_convenio'        , 'integer'   , true,  ''      , true , true);
    $this->AddCampo('cgm_beneficiario'         , 'integer'   , true,  ''      , true , true);
    $this->AddCampo('timestamp'                , 'timestamp_now' , true,  ''      , true , true);
    $this->AddCampo('grau_parentesco'          , 'integer'   , true,  ''      , false, true);
    $this->AddCampo('codigo_usuario'           , 'integer'   , true,  ''      , false, false);
    $this->AddCampo('dt_inicio'                , 'date'      , true,  ''      , false, false);
    $this->AddCampo('dt_fim'                   , 'date'      , false, ''      , false, false);
    $this->AddCampo('valor'                    , 'numeric'   , true,  '14,2'  , false, false);
    $this->AddCampo('timestamp_excluido'       , 'timestamp' , false, ''      , false, false);
    $this->AddCampo('cod_periodo_movimentacao' , 'integer'   , true,  ''      , false, false);
}

function limpaBeneficios( $boTransacao = "" )
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;

    $stSql  = " UPDATE beneficio.beneficiario                                                             \n";
    $stSql .= "    SET timestamp_excluido = '".$this->getDado('timestamp_exclusao')."'                                    \n";
    
    $stSql .= "  WHERE cod_contrato = ".$this->getDado('cod_contrato')." ";
    $stSql .= "    AND cgm_fornecedor = ".$this->getDado('cgm_fornecedor')." ";
    $stSql .= "    AND cod_modalidade = ".$this->getDado('cod_modalidade')." ";
    $stSql .= "    AND cod_tipo_convenio = ".$this->getDado('cod_tipo_convenio')." ";
    $stSql .= "    AND codigo_usuario = ".$this->getDado('codigo_usuario')." ";
    $stSql .= "    AND timestamp_excluido is NULL ";
//    $stSql .= "    AND timestamp = (SELECT MAX(timestamp) FROM beneficio.beneficiario AS BB WHERE BB.cod_contrato=beneficiario.cod_contrato AND BB.codigo_usuario=beneficiario.codigo_usuario ) ";
    
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaDML( $stSql, $boTransacao );

    return $obErro;
}
}
?>

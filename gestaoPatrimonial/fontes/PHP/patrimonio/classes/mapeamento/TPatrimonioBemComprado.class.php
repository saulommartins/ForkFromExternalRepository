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

    * Data de Criação: 12/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 25536 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-09-18 12:11:18 -0300 (Ter, 18 Set 2007) $

    * Casos de uso: uc-03.01.06
*/


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPatrimonioBemComprado extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPatrimonioBemComprado()
{
    parent::Persistente();
    $this->setTabela('patrimonio.bem_comprado');
    $this->setCampoCod('cod_bem');

    $this->AddCampo('cod_bem'          ,'integer',true,''     ,true ,true);
    $this->AddCampo('exercicio'        ,'char'   ,true,'4'    ,false,'TOrcamentoEntidade');
    $this->AddCampo('cod_entidade'     ,'integer',true,''     ,false,'TOrcamentoEntidade');
    $this->AddCampo('cod_empenho'      ,'integer',false,''    ,false,false);
    $this->AddCampo('num_orgao'        ,'integer',false,''    ,false,false);
    $this->AddCampo('num_unidade'      ,'integer',false,''    ,false,false);
    $this->AddCampo('nota_fiscal'      ,'varchar',false,'30'  ,false,false);
    $this->AddCampo('data_nota_fiscal' ,'date'   ,true ,''    ,false,false);
    $this->AddCampo('caminho_nf'       ,'varchar',true ,'100' ,false,false);
    
}

function recuperaEspecieAtributo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
     return $this->executaRecupera("montaRecuperaEspecieAtributo",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaEspecieAtributo()
{
    $stSql .= "
        SELECT cod_modulo
             , cod_cadastro
             , cod_atributo
             , cod_especie
             , cod_natureza
             , cod_grupo
          FROM patrimonio.especie_atributo
         WHERE ";
    if ( $this->getDado( 'cod_modulo' ) ) {
        $stSql .= " cod_modulo = ".$this->getDado( 'cod_modulo' )."  AND ";
    }
    if ( $this->getDado( 'cod_cadastro' ) ) {
        $stSql .= " cod_cadastro = ".$this->getDado( 'cod_cadastro' )."  AND ";
    }
    if ( $this->getDado( 'cod_atributo' ) ) {
        $stSql .= " cod_atributo = ".$this->getDado( 'cod_atributo' )."  AND ";
    }
    if ( $this->getDado( 'cod_especie' ) ) {
        $stSql .= " cod_especie = ".$this->getDado( 'cod_especie' )."  AND ";
    }
    if ( $this->getDado( 'cod_natureza' ) ) {
        $stSql .= " cod_natureza = ".$this->getDado( 'cod_natureza' )."  AND ";
    }
    if ( $this->getDado( 'cod_grupo' ) ) {
        $stSql .= " cod_grupo = ".$this->getDado( 'cod_grupo' )."  AND ";
    }
    if ( $this->getDado( 'nom_especie' ) ) {
        $stSql .= " nom_especie = '".$this->getDado( 'nom_especie' )."'  AND ";
    }
    if ( $this->getDado( 'ativo' ) ) {
        $stSql .= " ativo = '".$this->getDado( 'ativo' )."'  AND ";
    }

    return substr($stSql,0,-6);
}

}

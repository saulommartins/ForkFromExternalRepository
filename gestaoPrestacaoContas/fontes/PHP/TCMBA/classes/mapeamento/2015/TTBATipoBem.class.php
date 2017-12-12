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
    * Extensão da Classe de mapeamento
    * Data de Criação: 24/09/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 62952 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 09:58:01 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.05.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTBATipoBem extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTBATipoBem()
{
    parent::Persistente();
    $this->setTabela('tcmba.tipo_bem');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_natureza,cod_grupo,cod_tipo_tcm');

    $this->AddCampo('cod_tipo_tcm' ,'integer' ,true  ,'' ,true ,false);
    $this->AddCampo('cod_natureza' ,'integer' ,false ,'' ,true ,'TPatrimonioNatureza','cod_natureza');
    $this->AddCampo('cod_grupo'    ,'integer' ,false ,'' ,true ,'TPatrimonioGrupo','cod_grupo');
}

function montaRecuperaRelacionamento()
{
    $stSql = " SELECT *                                      
                  FROM ".$this->getTabela()." as tab        
             LEFT JOIN patrimonio.grupo as grup 
                    ON tab.cod_natureza = grup.cod_natureza
                   AND tab.cod_grupo    = grup.cod_grupo
              ORDER BY tab.cod_tipo_tcm ";

    return $stSql;
}

function recuperaNaturezaGrupo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaNaturezaGrupo().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaNaturezaGrupo()
{
    $stSql = '
        SELECT grupo.*
             , natureza.*
             , tipo_bem.cod_tipo_tcm
          FROM patrimonio.grupo
    
    INNER JOIN patrimonio.natureza
            ON grupo.cod_natureza = natureza.cod_natureza
    
     LEFT JOIN tcmba.tipo_bem
            ON tipo_bem.cod_natureza = natureza.cod_natureza
           AND tipo_bem.cod_grupo    = grupo.cod_grupo ';

    return $stSql;
}

}

?>
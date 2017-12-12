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
    * Classe de mapeamento do relatorio de Demonstrativo de Despesa - Anexo 11
    * Data de Criação: 19/02/2008

    * @author Analista: Tonismar R. Bernardo
    * @author Desenvolvedor: Tonismar R. Bernardo

    * @ignore

    $Id:$

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FTGOAnexo11 extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FTGOAnexo11()
{
    parent::Persistente();
    $this->setTabela('tcmgo.fn_rl_anexo11');

    $this->AddCampo( 'cod_estrutural'            ,'varchar' ,false, ''    , false,false );
    $this->AddCampo( 'descricaol'                ,'varchar' ,false, ''    , false,false );
    $this->AddCampo( 'nivel'                     ,'integer' ,false, ''    , false,false );
    $this->AddCampo( 'vl_original'               ,'numeric' ,false, '14.2', false,false );
    $this->AddCampo( 'vl_credito_orcamenteario'  ,'numeric' ,false, '14.2', false,false );
    $this->AddCampo( 'vl_credito_especial'       ,'numeric' ,false, '14.2', false,false );
    $this->AddCampo( 'vl_saldo_atual'            ,'numeric' ,false, '14.2', false,false );
    $this->AddCampo( 'vl_saldo_anterior'         ,'numeric' ,false, '14.2', false,false );
    $this->AddCampo( 'vl_realizado'              ,'numeric' ,false, '14.2', false,false );
    $this->AddCampo( 'situacao'     			 ,'varchar' ,false,	''    , false,false );
}

function montaRecuperaTodos()
{
    $stSql  = " SELECT                                                                                      \n";
    $stSql .= "     *                                                                                       \n";
    $stSql .= " FROM                                                                                        \n";
    $stSql .= "".$this->getTabela()."('".$this->getDado("exercicio")."',
              '".$this->getDado("stFiltro")."', '".$this->getDado("stDtInicial")."',
              '".$this->getDado("stDtFinal")."', '".$this->getDado('stSituacao') ."',
              '".$this->getDado('stCodEntidade')."' )                           \n";
    $stSql .= "     as retorno( cod_estrutural varchar                                                      \n";
    $stSql .= "                ,descricao varchar                                                           \n";
    $stSql .= "                ,nivel integer                                                               \n";
    $stSql .= "                ,vl_original numeric                                                         \n";
    $stSql .= "                ,vl_credito_orcamentario numeric                                             \n";
    $stSql .= "                ,vl_credito_especial numeric                                                 \n";
    $stSql .= "                ,vl_realizado numeric                                                        \n";
    $stSql .= "     )                                                                                       \n";

    return $stSql;
}

}
?>

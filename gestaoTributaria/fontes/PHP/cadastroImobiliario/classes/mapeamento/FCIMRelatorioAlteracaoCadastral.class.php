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
  * Página de
  * Data de criação : 21/06/2005

    * @author Analista: Fabio Bertoldi
    * @author Programador: Fernando Zank Correa Evangelista

    * $Id: FCIMRelatorioAlteracaoCadastral.class.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-05.01.25
**/

/*
$Log$
Revision 1.6  2006/09/18 09:12:53  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

set_time_limit(0);

class FCIMRelatorioAlteracaoCadastral extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FCIMRelatorioAlteracaoCadastral()
{
    parent::Persistente();
    $this->setTabela('imobiliario.fn_rl_alteracao_cadastral');

    $this->setCampoCod('');
    $this->setComplementoChave('');

    $this->AddCampo( 'inscricao_municipal' ,'integer', true, '',false, false );
    $this->AddCampo( 'proprietario_cota'   ,'varchar', true, '',false, false );
    $this->AddCampo( 'numero'              ,'integer', true, '',false, false );
    $this->AddCampo( 'complemento'         ,'varchar', true, '',false, false );
    $this->AddCampo( 'cod_lote'            ,'integer', true, '',false, false );
    $this->AddCampo( 'tipo_lote'           ,'varchar', true, '',false, false );
    $this->AddCampo( 'cod_localizacao'     ,'integer', true, '',false, false );
    $this->AddCampo( 'localizacao'         ,'varchar', true, '',false, false );
    $this->AddCampo( 'cod_condominio'      ,'integer', true, '',false, false );
    $this->AddCampo( 'creci'               ,'integer', true, '',false, false );
    $this->AddCampo( 'nom_bairro'          ,'varchar', true, '',false, false );
    $this->AddCampo( 'logradouro'          ,'varchar', true, '',false, false );
    $this->AddCampo( 'situacao'            ,'varchar', true, '',false, false );
}

function montaRecuperaTodos()
{
    $stSql  = "SELECT *                                                              \n";
    $stSql .= "FROM ".$this->getTabela()."( '".$this->getDado('stFiltroLote')."'     \n";
    $stSql .= "                            ,'".$this->getDado('stFiltroImovel')."'   \n";
    $stSql .= "                            ,'".$this->getDado('stDistinct')."'       \n";
    $stSql .= ") as retorno( inscricao_municipal  integer                            \n";
    $stSql .= "             ,proprietario_cota    text                               \n";
    $stSql .= "             ,cod_lote             integer                            \n";
    $stSql .= "             ,tipo_lote            text                               \n";
    $stSql .= "             ,numero               varchar                            \n";
    $stSql .= "             ,complemento          varchar                            \n";
    $stSql .= "             ,cod_localizacao      integer                            \n";
    $stSql .= "             ,localizacao          text                               \n";
    $stSql .= "             ,cod_condominio       integer                            \n";
    $stSql .= "             ,creci                varchar                            \n";
    $stSql .= "             ,nom_bairro           varchar                            \n";
    $stSql .= "             ,logradouro           text                               \n";
    $stSql .= "             ,situacao             text                               \n";
    $stSql .= ")                                                                     \n";

    return $stSql;
}

}

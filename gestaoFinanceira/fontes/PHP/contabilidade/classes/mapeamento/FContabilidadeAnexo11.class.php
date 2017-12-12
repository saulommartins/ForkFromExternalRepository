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
    * Classe de mapeamento da tabela FN_CONTABILIDADE_ANEXO11
    * Data de Criação: 12/05/2005

    * @author Analista: Gelson Wolowski
    * @author Desenvolvedor: Marcelo B. Paulino

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: bruce $
    $Date: 2007-08-03 15:31:33 -0300 (Sex, 03 Ago 2007) $

    * Casos de uso: uc-02.02.08
*/

/*
$Log$
Revision 1.9  2007/08/03 18:31:23  bruce
Bug#9243#

Revision 1.8  2007/07/27 15:17:53  bruce
Bug#9243#

Revision 1.7  2007/02/08 17:44:05  luciano
#6721#

Revision 1.6  2006/07/05 20:50:14  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FContabilidadeAnexo11 extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FContabilidadeAnexo11()
{
    parent::Persistente();
    $this->setTabela('contabilidade.fn_rl_demonstrativo_despesa');

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
    $stSql .= "   ".$this->getTabela()."('".$this->getDado("exercicio")."','".$this->getDado("stFiltro")."','".$this->getDado("stDtInicial")."','".$this->getDado("stDtFinal")."','"  .$this->getDado('stSituacao') ."' )\n";
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

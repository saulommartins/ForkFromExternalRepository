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
    * Classe de mapeamento da tabela
    * Data de Criação: 24/01/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Autor:$
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-06.03.00

*/

/*
$Log$
Revision 1.1  2007/04/27 18:30:31  hboaventura
Arquivos para geração do TCEPB

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTPBTipoOrigemRecurso extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTPBTipoOrigemRecurso()
{
    parent::Persistente();
    $this->setTabela("tcepb.tipo_origem_recurso");

    $this->setCampoCod('cod_tipo');

    $this->AddCampo('cod_tipo','integer',true,'4',true,false);
    $this->AddCampo('exercicio','varchar',true,'4',true,false);
    $this->AddCampo('descricao','varchar',true,'',false,false);
}

function montaRecuperaTodos()
{
    $stSql  = " SELECT *                                                \n";
    $stSql .= "   FROM tcepb.tipo_origem_recurso                        \n";
    $stSql .= "  WHERE exercicio = ".$this->getDado('exercicio')."      \n";

    return $stSql;
}

}

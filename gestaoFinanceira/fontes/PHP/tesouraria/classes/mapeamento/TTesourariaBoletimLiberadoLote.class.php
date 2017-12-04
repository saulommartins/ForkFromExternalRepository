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
    * Classe de mapeamento da tabela tesouraria.boletim_liberado_lote
    * Data de Criação: 09/07/2007

    * @author Analista: Gelson W.
    * @author Desenvolvedor: Anderson Konze

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2007-07-13 16:18:44 -0300 (Sex, 13 Jul 2007) $

    * Casos de uso: uc-02.04.04,uc-02.04.08
*/
/*
$Log$
Revision 1.1  2007/07/13 19:12:41  cako
Bug#9383#, Bug#9384#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTesourariaBoletimLiberadoLote extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaBoletimLiberadoLote()
{
    parent::Persistente();
    $this->setTabela("tesouraria.boletim_liberado_lote");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_entidade,cod_boletim,timestamp_fechamento,timestamp_liberado,exercicio,tipo,cod_lote');

    $this->AddCampo('cod_entidade'        ,'integer'  ,true  ,''   ,true,'TContabilidadeLote');
    $this->AddCampo('cod_boletim'         ,'integer'  ,true  ,''   ,true,'TTesourariaBoletimLiberado');
    $this->AddCampo('timestamp_fechamento','timestamp',true  ,''   ,true,'TTesourariaBoletimLiberado');
    $this->AddCampo('timestamp_liberado'  ,'timestamp',true  ,''   ,true,'TTesourariaBoletimLiberado');
    $this->AddCampo('exercicio'           ,'char'     ,true  ,'4'  ,true,'TContabilidadeLote');
    $this->AddCampo('tipo'                ,'char'     ,true  ,'1'  ,true,'TContabilidadeLote');
    $this->AddCampo('cod_lote'            ,'integer'  ,true  ,''   ,true,'TContabilidadeLote');

}
}
?>

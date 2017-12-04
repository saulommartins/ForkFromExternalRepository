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
    * Classe de mapeamento da tabela licitacao.anulacao_impugnacao_edital
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 18203 $
    $Name$
    $Author: hboaventura $
    $Date: 2006-11-27 10:03:26 -0200 (Seg, 27 Nov 2006) $

    * Casos de uso: uc-03.05.27
*/
/*
$Log$
Revision 1.3  2006/11/27 12:01:23  hboaventura
Implementação do caso de uso 03.05.27

Revision 1.2  2006/11/08 10:51:41  larocca
Inclusão dos Casos de Uso

Revision 1.1  2006/09/15 12:05:59  cleisson
inclusão

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TLicitacaoAnulacaoImpugnacaoEdital extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TLicitacaoAnulacaoImpugnacaoEdital()
{
    parent::Persistente();
    $this->setTabela("licitacao.anulacao_impugnacao_edital");

    $this->setCampoCod('');
    $this->setComplementoChave('num_edital,exercicio,cod_processo,exercicio_processo');

    $this->AddCampo('num_edital'         ,'integer',false ,''   ,true,'TLicitacaoEditalImpugnado');
    $this->AddCampo('exercicio'          ,'varchar'   ,false ,'4'  ,true,'TLicitacaoEditalImpugnado');
    $this->AddCampo('cod_processo'       ,'integer',false ,''  ,true,'TLicitacaoEditalImpugnado');
    $this->AddCampo('exercicio_processo' ,'char'   ,false ,'4'  ,true,'TLicitacaoEditalImpugnado');
    $this->AddCampo('parecer_juridico'   ,'text'   ,false ,''   ,false,false);

}

}

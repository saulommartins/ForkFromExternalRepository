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
    * Classe de mapeamento da tabela licitacao.participante_consorcio
    * Data de Criação: 09/11/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Maicon Brauwers

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-03.05.18
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.participante_consorcio
  * Data de Criação: 15/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Maicon Brauwers

  * @package URBEM
  * @subpackage Mapeamento
*/
class TLicitacaoParticipanteConsorcio extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TLicitacaoParticipanteConsorcio()
{
    parent::Persistente();
    $this->setTabela("licitacao.participante_consorcio");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_licitacao,cgm_fornecedor,cod_modalidade,cod_entidade,exercicio,numcgm');

    $this->AddCampo('cod_licitacao'       ,'integer',false ,''   ,true,'TLicitacaoParticipante');
    $this->AddCampo('cgm_fornecedor'      ,'integer',false ,''   ,true,'TLicitacaoParticipante');
    $this->AddCampo('cod_modalidade'      ,'integer',false ,''   ,true,'TLicitacaoParticipante');
    $this->AddCampo('cod_entidade'        ,'integer',false ,''   ,true,'TLicitacaoParticipante');
    $this->AddCampo('exercicio'           ,'char'   ,false ,'4'  ,true,'TLicitacaoParticipante');
    $this->AddCampo('numcgm'              ,'integer',false ,''   ,false,'TCGMCGM','numcgm');
}
}

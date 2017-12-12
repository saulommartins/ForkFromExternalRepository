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
     * Classe de mapeamento para a tabela ECONOMICO.VW_LICENCA_SUSPENSA_ATIVA
     * Data de Criação: 01/12/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Fábio Bertoldi Rodrigues

     * @package URBEM
     * @subpackage Mapeamento

    * $Id: VCEMLicencaSuspensaAtiva.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.02.12
*/

/*
$Log$
Revision 1.6  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a view ECONOMICO.VW_LICENCA_SUSPENSA_ATIVA
  * Data de Criação: 01/12/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Fábio Bertoldi Rodrigues

  * @package URBEM
  * @subpackage Mapeamento
*/
class VCEMLicencaSuspensaAtiva extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function VCEMLicencaSuspensaAtiva()
{
    parent::Persistente();
    $this->setTabela('ECONOMICO.VW_LICENCA_SUSPENSA_ATIVA');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_licenca,exercicio');

    $this->AddCampo('cod_licenca'        ,'integer'   );
    $this->AddCampo('exercicio'          ,'varchar'   );
    $this->AddCampo('dt_inicio'          ,'date'      );
    $this->AddCampo('dt_termino'         ,'date'      );
    $this->AddCampo('cod_processo'       ,'integer'   );
    $this->AddCampo('exercicio_processo' ,'varchar'   );
    $this->AddCampo('especie_licenca'    ,'varchar'   );
    $this->AddCampo('inscricao_economica','integer'   );
    $this->AddCampo('numcgm'             ,'integer'   );
    $this->AddCampo('nom_cgm'            ,'varchar'   );
    $this->AddCampo('cod_tipo_diversa'   ,'integer'   );
    $this->AddCampo('cod_processo_baixa' ,'integer'   );
    $this->AddCampo('dt_susp_inicio'     ,'date'      );
    $this->AddCampo('dt_susp_termino'    ,'date'      );
    $this->AddCampo('motivo'             ,'text'      );

}

}

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
    * Classe de mapeamento da tabela TESOURARIA.CONCILIACAO_LANCAMENTO_ARRECADACAO
    * Data de Criação: 23/02/2006

    * @author Analista: Anderson C. Konze

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2007-09-14 18:02:47 -0300 (Sex, 14 Set 2007) $

    * Casos de uso: uc-02.04.19
*/

/*
$Log$
Revision 1.1  2007/09/14 20:56:57  cako
Ticket#9496#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTesourariaConciliacaoLancamentoArrecadacaoEstornada extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaConciliacaoLancamentoArrecadacaoEstornada()
{
    parent::Persistente();
    $this->setTabela("tesouraria.conciliacao_lancamento_arrecadacao_estornada");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_plano,exercicio,mes,cod_arrecadacao,timestamp_arrecadacao,timestamp_estornada,tipo');

    $this->AddCampo('cod_plano'             , 'integer'  , true, ''  , true, true );
    $this->AddCampo('exercicio'             , 'varchar'  , true, '04', true, true );
    $this->AddCampo('exercicio_conciliacao' , 'varchar'  , true, '04', true, true );
    $this->AddCampo('mes'                   , 'integer'  , true, ''  , true, true );
    $this->AddCampo('cod_arrecadacao'       , 'integer'  , true, ''  , true, true );
    $this->AddCampo('timestamp_arrecadacao' , 'timestamp', true, ''  , true, true );
    $this->AddCampo('timestamp_estornada'   , 'timestamp', true, ''  , true, true );
    $this->AddCampo('tipo'                  , 'varchar'  , true, '01', true, true );

}

}

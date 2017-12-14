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
    * Classe de mapeamento da tabela ARRECADACAO.LANCAMENTO_CONCEDE_DESONERACAO
    * Data de Criação: 07/06/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRLancamentoConcedeDesoneracao.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.05
*/

/*
$Log$
Revision 1.3  2006/09/15 10:41:36  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TARRLancamentoConcedeDesoneracao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TARRLancamentoConcedeDesoneracao()
{
    parent::Persistente();
    $this->setTabela("arrecadacao.lancamento_concede_desoneracao");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_lancamento, cod_calculo, cod_desoneracao, numcgm, ocorrencia');
                                    //nome',  tipo    requerido   Tamanho   PK     FK    Conteudo = ''
    $this->AddCampo('cod_lancamento',   'integer', true,'' ,     true,  true );
    $this->AddCampo('cod_calculo',          'integer', true,'' ,    true,  true );
    $this->AddCampo('cod_desoneracao','integer', true,'' ,    true,  true );
    $this->AddCampo('numcgm',               'integer', true,'' ,    true,  true );
    $this->AddCampo('ocorrencia',           'integer', true,'' ,    true,  true );

}

}
?>

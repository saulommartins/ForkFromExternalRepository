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
    * Extensão da Classe de mapeamento
    * Data de Criação: 30/01/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Cleisson Barboza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-06.03.00
*/

/*
$Log$
Revision 1.2  2007/04/23 15:19:13  rodrigo_sr
uc-06.03.00

Revision 1.1  2007/03/22 00:33:09  cleisson
novos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTPBAditivos extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTPBAditivos()
{
    parent::Persistente();
    $this->setDado('exercicio', Sessao::getExercicio() );
}

//Mapeamento do case pode ser encontrado no documento de tabelas auxiliares do tribunal
function montaRecuperaTodos()
{
    $stSql  = " SELECT                                                          \n";
    $stSql .= "     num_contrato || exercicio as num_contrato,                  \n";
    $stSql .= "     num_aditivo || exercicio as num_aditivo,                    \n";
    $stSql .= "     TO_CHAR(dt_assinatura, 'DDMMYYYY') as dt_assinatura,        \n";
    $stSql .= "     objeto || ' ' || fundamentacao as motivo_justificativa,     \n";
    $stSql .= "     valor_contratado                                            \n";
    $stSql .= " FROM                                                            \n";
    $stSql .= "     licitacao.contrato_aditivos                                 \n";
    $stSql .= " WHERE                                                           \n";

    if ( $this->getDado('exercicio') ) {
        $stSql .= " exercicio = '".$this->getDado('exercicio')."'               \n";
    }

    if ( $this->getDado('stEntidades') ) {
        $stSql .= " AND cod_entidade in (".$this->getDado('stEntidades').")     \n";
    }

    if ( $this->getDado('inMes') ) {
        $stSql .= " AND to_char(dt_assinatura,'mm') = '".$this->getDado('inMes')."'  \n";
    }

    return $stSql;
}
}

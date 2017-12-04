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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TLicitacaoContratoArquivo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TLicitacaoContratoArquivo()
{
    parent::Persistente();
    $this->setTabela("licitacao.contrato_arquivo");

    $this->setCampoCod('arquivo');
    $this->setComplementoChave('num_contrato, cod_entidade, exercicio');

    $this->AddCampo('num_contrato' ,'sequence',false, ''   , true, true);
    $this->AddCampo('cod_entidade' ,'integer' ,false, ''   , true, true);
    $this->AddCampo('exercicio'    ,'varchar' ,false, '4'  , true, true);
    $this->AddCampo('nom_arquivo'  ,'varchar' ,false, '120', true, false);
    $this->AddCampo('arquivo'      ,'varchar' ,false, '200', true, false);
}

function excluirArquivos()
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql = $this->montaExcluirArquivos();
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaDML( $stSql, $boTransacao );

    return $obErro;
}

///arrumar
function montaExcluirArquivos()
{
    $stSql = "DELETE FROM licitacao.contrato_arquivo  \n";
    $stSql.= "      WHERE contrato_arquivo.num_contrato = ".$this->getDado('num_contrato')." \n";
    $stSql.= "        AND contrato_arquivo.cod_entidade = ".$this->getDado('cod_entidade')." \n";
    $stSql.= "        AND contrato_arquivo.exercicio    = '".$this->getDado('exercicio')."'  \n";

    return $stSql;
}

}

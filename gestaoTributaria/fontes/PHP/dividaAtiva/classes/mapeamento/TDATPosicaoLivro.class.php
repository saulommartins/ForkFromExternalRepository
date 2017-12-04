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
    * Classe de mapeamento da tabela DIVIDA.POSICAO_LIVRO
    * Data de Criação: 28/09/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TDATPosicaoLivro.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.04.02
*/

/*
$Log:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TDATPosicaoLivro extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TDATPosicaoLivro()
    {
        parent::Persistente();
        $this->setTabela('divida.posicao_livro');

        $this->setCampoCod('num_livro');
        #$this->setComplementoChave('num_livro');
        $this->setComplementoChave('');

        $this->AddCampo('num_livro'	,'integer',true,'',true,false);
        $this->AddCampo('num_pagina','integer',true,'',false,false);

    }

    public function recuperaPosicaoLivro(&$rsRecordset, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;

        $stSql  = $this->montaRecuperaPosicaoLivro();
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordset, $stSql, $boTransacao );

    return $obErro;
    }

    public function montaRecuperaPosicaoLivro()
    {
        $stSql  = " SELECT                                                              \r\n";
        $stSql .= "     divida.fn_busca_livro_pagina() as valor  \r\n";

    return $stSql;
    }

}// end of class

?>

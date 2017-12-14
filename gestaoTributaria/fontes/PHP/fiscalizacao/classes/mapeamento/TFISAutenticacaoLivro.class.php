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
    * Classe de regra de mapeamento para FISCALIZACAO.AUTENTICACAO_LIVRO
    * Data de Criacao: 06/08/2007

    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Rodrigo D.S.
    * @ignore

    * $Id: TAutenticacaoLivro.class.php 29237 2008-04-16 12:02:48Z fabio $

    *Casos de uso: uc-05.07.04

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE                                                                      );

class TFISAutenticacaoLivro extends Persistente
{
/**
    * Metodo Construtor
    * @access Private
*/
    public function TFISAutenticacaoLivro()
    {
        parent::Persistente();
        $this->setTabela('fiscalizacao.autenticacao_livro');

        $this->setCampoCod('inscricao_economica,nr_livro');
        $this->setComplementoChave('inscricao_economica');

        $this->AddCampo( 'inscricao_economica','integer',true,'',true,false );
        $this->AddCampo( 'nr_livro','integer',true,'',true,false            );
        $this->AddCampo( 'periodo_inicio','date',true,'',false,false        );
        $this->AddCampo( 'periodo_termino','date',true,'',false,false       );
        $this->AddCampo( 'qtd_paginas','integer',true,'',true,false         );
        $this->AddCampo( 'observacao','text',true,'',false,false            );
    }
}

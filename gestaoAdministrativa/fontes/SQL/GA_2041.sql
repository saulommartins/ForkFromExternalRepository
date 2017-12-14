/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - SoluÃ§Ãµes em GestÃ£o PÃºblica                                *
    * @copyright (c) 2013 ConfederaÃ§Ã£o Nacional de MunicÃ­pos                         *
    * @author ConfederaÃ§Ã£o Nacional de MunicÃ­pios                                    *
    *                                                                                *
    * O URBEM CNM Ã© um software livre; vocÃª pode redistribuÃ­-lo e/ou modificÃ¡-lo sob *
    * os  termos  da LicenÃ§a PÃºblica Geral GNU conforme  publicada  pela FundaÃ§Ã£o do *
    * Software Livre (FSF - Free Software Foundation); na versÃ£o 2 da LicenÃ§a.       *
    *                                                                                *
    * Este  programa  Ã©  distribuÃ­do  na  expectativa  de  que  seja  Ãºtil,   porÃ©m, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implÃ­cita  de  COMERCIABILIDADE  OU *
    * ADEQUAÃÃO A UMA FINALIDADE ESPECÃFICA. Consulte a LicenÃ§a PÃºblica Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * VocÃª deve ter recebido uma cÃ³pia da LicenÃ§a PÃºblica Geral do GNU "LICENCA.txt" *
    * com  este  programa; se nÃ£o, escreva para  a  Free  Software Foundation  Inc., *
    * no endereÃ§o 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
/*
*
* Script de DDL e DML
*
* Versao 2.04.1
*
* Fabio Bertoldi - 20150625
*
*/

----------------
-- Ticket #23076
----------------

UPDATE administracao.funcionalidade SET nom_funcionalidade = 'Manutenção do CGM' WHERE cod_funcionalidade = 13;


----------------
-- Ticket #23077
----------------

UPDATE sw_tipo_logradouro SET nom_tipo = 'Não Informado' WHERE cod_tipo = 0;

UPDATE administracao.funcionalidade SET ordem = 5 WHERE cod_funcionalidade = 40;

INSERT
  INTO administracao.funcionalidade
     ( cod_funcionalidade
     , cod_modulo
     , nom_funcionalidade
     , nom_diretorio
     , ordem
     , ativo
     )
VALUES
     ( 492
     , 4
     , 'Manutenção de Logradouro'
     , 'instancias/logradouro/'
     , 3
     , TRUE
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
VALUES
     ( 3059
     , 492
     , 'FMManterLogradouro.php'
     , 'incluir'
     , 1
     , ''
     , 'Incluir Logradouro'
     , TRUE
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
VALUES
     ( 3060
     , 492
     , 'FLManterLogradouro.php'
     , 'alterar'
     , 2
     , ''
     , 'Alterar Logradouro'
     , TRUE
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
VALUES
     ( 3061
     , 492
     , 'FLManterLogradouro.php'
     , 'consultar'
     , 3
     , ''
     , 'Consultar Logradouro'
     , TRUE
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
VALUES
     ( 3062
     , 492
     , 'FLManterLogradouro.php'
     , 'excluir'
     , 4
     , ''
     , 'Excluir Logradouro'
     , TRUE
     );


----------------
-- Ticket #23078
----------------

INSERT
  INTO administracao.funcionalidade
     ( cod_funcionalidade
     , cod_modulo
     , nom_funcionalidade
     , nom_diretorio
     , ordem
     , ativo
     )
VALUES
     ( 493
     , 4
     , 'Manutenção de Bairro'
     , 'instancias/bairro/'
     , 4
     , TRUE
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
VALUES
     ( 3063
     , 493
     , 'FMManterBairro.php'
     , 'incluir'
     , 1
     , ''
     , 'Incluir Bairro'
     , TRUE
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
VALUES
     ( 3064
     , 493
     , 'FLManterBairro.php'
     , 'alterar'
     , 2
     , ''
     , 'Alterar Bairro'
     , TRUE
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
VALUES
     ( 3065
     , 493
     , 'FLManterBairro.php'
     , 'consultar'
     , 3
     , ''
     , 'Consultar Bairro'
     , TRUE
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
VALUES
     ( 3066
     , 493
     , 'FLManterBairro.php'
     , 'excluir'
     , 4
     , ''
     , 'Excluir Bairro'
     , TRUE
     );


----------------
-- Ticket #23074
----------------

UPDATE administracao.modulo SET nom_modulo = 'URBEM' WHERE cod_modulo = 0;

